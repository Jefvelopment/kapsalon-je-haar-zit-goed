<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Treatment;
use App\Models\User;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function __construct(protected ScheduleService $scheduleService)
    {
    }

    /**
     * Publieke kalenderweergave. Voor iedereen bereikbaar, maar de inhoud van
     * bezette sloten verschilt per rol:
     * - Gast/customer: bezette sloten zijn anoniem (geen naam/behandeling te zien).
     * - Employee: bezette sloten zijn anoniem, behalve hun eigen afspraken.
     * - Owner: ziet alles met volledige info.
     */
    public function index(Request $request)
    {
        $startOfWeek = $request->has('week')
            ? Carbon::parse($request->query('week'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $week = $this->scheduleService->slotsForWeek($startOfWeek);

        $user = Auth::user();
        $role = $user?->role;

        // Verrijk elk slot met de info die deze gebruiker mag zien.
        $week = $week->map(function ($day) use ($role, $user) {
            $day['slots'] = $day['slots']->map(function ($slot) use ($role, $user) {
                if ($slot['status'] === 'booked' && $slot['appointment']) {
                    $appointment = $slot['appointment'];

                    $canSeeDetails = $role === Role::Owner
                        || ($role === Role::Employee && $appointment->employee_id === $user->id);

                    $slot['visible_appointment'] = $canSeeDetails ? $appointment : null;
                }

                return $slot;
            });

            return $day;
        });

        return view('appointments.index', [
            'week'        => $week,
            'startOfWeek' => $startOfWeek,
            'prevWeek'    => $startOfWeek->copy()->subWeek()->toDateString(),
            'nextWeek'    => $startOfWeek->copy()->addWeek()->toDateString(),
        ]);
    }

    public function view($id)
    {
        $appointment = Appointment::with(['user', 'employee', 'treatments'])->findOrFail($id);
        return view('appointments.view', compact('appointment'));
    }

    /**
     * Toon het boekingsformulier voor een specifiek datum + tijdslot.
     * Vereist login (afgedwongen via route middleware).
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $date = Carbon::parse($validated['date']);

        // Check direct of dit slot nog steeds beschikbaar is (kan in de tussentijd gewijzigd zijn).
        $treatments = Treatment::all();
        $employees  = User::where('role', Role::Employee)->get();
        $customers  = Auth::user()->role === Role::Owner
            ? User::where('role', Role::Customer)->orderBy('name')->get()
            : collect();

        // Minimale duur om te checken: kleinste behandelingsduur, of slotlengte als fallback.
        $minDuration = $treatments->min('duration_minutes') ?? 30;

        if (!$this->scheduleService->isRangeAvailable($date, $validated['time'], $minDuration)) {
            return redirect()->route('appointments')
                ->withErrors(['slot' => 'Dit tijdslot is niet meer beschikbaar.']);
        }

        return view('appointments.create', [
            'date'       => $validated['date'],
            'time'       => $validated['time'],
            'treatments' => $treatments,
            'employees'  => $employees,
            'customers'  => $customers,
        ]);
    }

    /**
     * Sla een nieuwe afspraak op. Vereist login (afgedwongen via route middleware).
     *
     * Normaal gesproken is de afspraak voor de ingelogde gebruiker zelf.
     * Een owner mag echter een afspraak namens een specifieke klant inplannen
     * (bijv. telefonische boekingen), door 'user_id' in het formulier mee te geven.
     */
    public function store(Request $request)
    {
        $isOwner = Auth::user()->role === Role::Owner;

        $validated = $request->validate([
            'date'         => 'required|date',
            'time'         => 'required|date_format:H:i',
            'employee_id'  => 'nullable|exists:users,id',
            'treatments'   => 'required|array|min:1',
            'treatments.*' => 'exists:treatments,id',
            // user_id mag alleen door de owner gebruikt worden; voor klanten wordt dit genegeerd.
            'user_id'      => $isOwner ? 'required|exists:users,id' : 'nullable',
        ]);

        $date = Carbon::parse($validated['date']);

        $selectedTreatments = Treatment::whereIn('id', $validated['treatments'])->get();
        $totalDuration = $selectedTreatments->sum('duration_minutes');

        // Herbevestig beschikbaarheid server-side met de werkelijke totale duur,
        // om dubbele boekingen / race conditions te voorkomen.
        if (!$this->scheduleService->isRangeAvailable($date, $validated['time'], $totalDuration)) {
            return redirect()->back()
                ->withErrors(['slot' => 'Dit tijdslot is niet meer beschikbaar voor de gekozen behandeling(en).'])
                ->withInput();
        }

        // Owner mag boeken namens een klant; iedere andere rol boekt altijd voor zichzelf.
        $appointmentUserId = $isOwner ? $validated['user_id'] : Auth::id();

        $appointment = Appointment::create([
            'user_id'     => $appointmentUserId,
            'employee_id' => $validated['employee_id'] ?? null,
            'date'        => $validated['date'],
            'time'        => $validated['time'],
        ]);

        $appointment->treatments()->sync($validated['treatments']);

        return redirect()->route('dashboard')->with('success', 'Afspraak ingepland.');
    }

    public function edit($id)
    {
        $appointment = Appointment::with(['user', 'employee', 'treatments'])->findOrFail($id);
        $treatments = Treatment::all();
        $employees = User::where('role', Role::Employee)->get();

        return view('appointments.edit', compact('appointment', 'treatments', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'nullable|exists:users,id',
            'date'        => 'required|date',
            'time'        => 'required|date_format:H:i',
            'treatments'  => 'nullable|array',
            'treatments.*'=> 'exists:treatments,id',
        ]);

        $date = Carbon::parse($validated['date']);

        $selectedTreatments = Treatment::whereIn('id', $validated['treatments'] ?? [])->get();
        $totalDuration = $selectedTreatments->sum('duration_minutes') ?: 30;

        // Check beschikbaarheid, maar sluit deze afspraak zelf uit van de check
        // (anders conflicteert een afspraak altijd met zijn eigen huidige tijdslot).
        if (!$this->scheduleService->isRangeAvailable($date, $validated['time'], $totalDuration, $appointment->id)) {
            return redirect()->back()
                ->withErrors(['slot' => 'Dit tijdslot is al bezet door een andere afspraak.'])
                ->withInput();
        }

        $appointment->update([
            'employee_id' => $validated['employee_id'],
            'date'        => $validated['date'],
            'time'        => $validated['time'],
        ]);

        $appointment->treatments()->sync($validated['treatments'] ?? []);

        return redirect()->route('dashboard')->with('success', 'Afspraak bijgewerkt.');
    }
}
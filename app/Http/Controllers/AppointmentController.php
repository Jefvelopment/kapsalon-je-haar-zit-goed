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

    public function index(Request $request)
    {
        $startOfWeek = $request->filled('week')
            ? Carbon::parse($request->query('week'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $week = $this->scheduleService->slotsForWeek($startOfWeek);

        $user = Auth::user();
        $role = $user?->role;

        $week = $week->map(function ($day) use ($role, $user) {
            $day['slots'] = $day['slots']->map(function ($slot) use ($role, $user) {
                if ($slot['status'] === 'booked' && $slot['appointment']) {
                    $appointment = $slot['appointment'];

                    $canSeeDetails =
                        $role === Role::Owner ||
                        ($role === Role::Employee && $appointment->employee_id === $user->id);

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
        $appointment = Appointment::with(['user', 'employee', 'treatments'])
            ->findOrFail($id);

        return view('appointments.view', compact('appointment'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $date = Carbon::parse($validated['date']);

        $treatments = Treatment::all();
        $employees  = User::where('role', Role::Employee)->get();

        $minDuration = $treatments->min('duration_minutes') ?? 30;

        if (!$this->scheduleService->isRangeAvailable(
            $date,
            $validated['time'],
            $minDuration
        )) {
            return redirect()
                ->route('appointments')
                ->withErrors(['slot' => 'Dit tijdslot is niet beschikbaar.'])
                ->withInput();
        }

        return view('appointments.create', [
            'date'       => $validated['date'],
            'time'       => $validated['time'],
            'treatments' => $treatments,
            'employees'  => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'time'         => 'required|date_format:H:i',
            'employee_id'  => 'nullable|integer|exists:users,id',
            'treatments'   => 'required|array|min:1',
            'treatments.*' => 'integer|exists:treatments,id',
        ]);

        $date = Carbon::parse($validated['date']);

        $selectedTreatments = Treatment::whereIn('id', $validated['treatments'])->get();
        $totalDuration = $selectedTreatments->sum('duration_minutes');

        if (!$this->scheduleService->isRangeAvailable(
            $date,
            $validated['time'],
            $totalDuration
        )) {
            return redirect()
                ->back()
                ->withErrors(['slot' => 'Tijdslot niet beschikbaar voor gekozen behandelingen.'])
                ->withInput();
        }

        $appointment = Appointment::create([
            'user_id'     => Auth::id(),
            'employee_id' => $validated['employee_id'] ?? null,
            'date'        => $validated['date'],
            'time'        => $validated['time'],
        ]);

        $appointment->treatments()->sync($validated['treatments']);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Afspraak ingepland.');
    }

    public function edit($id)
    {
        $appointment = Appointment::with(['user', 'employee', 'treatments'])
            ->findOrFail($id);

        $treatments = Treatment::all();
        $employees = User::where('role', Role::Employee)->get();

        return view('appointments.edit', compact('appointment', 'treatments', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'nullable|integer|exists:users,id',
            'date'        => 'required|date',
            'time'        => 'required|date_format:H:i',
            'treatments'  => 'nullable|array',
            'treatments.*'=> 'integer|exists:treatments,id',
        ]);

        $appointment->update([
            'employee_id' => $validated['employee_id'] ?? null,
            'date'        => $validated['date'],
            'time'        => $validated['time'],
        ]);

        $appointment->treatments()->sync($validated['treatments'] ?? []);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Afspraak bijgewerkt.');
    }
}
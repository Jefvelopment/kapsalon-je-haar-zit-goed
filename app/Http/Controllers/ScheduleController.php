<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use App\Models\ScheduleBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function edit()
    {
        $businessHours = BusinessHour::orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $blocks = ScheduleBlock::orderBy('date')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('schedule.edit', compact('businessHours', 'blocks'));
    }

    public function updateBusinessHours(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|array|size:7',

            'days.*.day_of_week'  => 'required|integer|min:0|max:6',
            'days.*.is_open'      => 'nullable|boolean',

            'days.*.start_time'   => 'nullable|date_format:H:i',
            'days.*.end_time'     => 'nullable|date_format:H:i|after:days.*.start_time',

            'days.*.slot_minutes' => 'required|integer|min:5|max:240',
        ]);

        foreach ($validated['days'] as $day) {
            $isOpen = $day['is_open'] ?? false;

            BusinessHour::updateOrCreate(
                ['day_of_week' => (int) $day['day_of_week']],
                [
                    'is_open'      => (bool) $isOpen,
                    'start_time'   => $isOpen ? ($day['start_time'] ?? '00:00') : '00:00',
                    'end_time'     => $isOpen ? ($day['end_time'] ?? '00:00') : '00:00',
                    'slot_minutes' => (int) $day['slot_minutes'],
                ]
            );
        }

        return redirect()
            ->route('schedule.edit')
            ->with('success', 'Openingstijden bijgewerkt.');
    }

    public function storeBlock(Request $request)
    {
        $validated = $request->validate([
            'is_recurring' => 'required|boolean',

            'date'        => 'required_if:is_recurring,0|nullable|date',
            'day_of_week' => 'required_if:is_recurring,1|nullable|integer|min:0|max:6',

            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',

            'reason'      => 'nullable|string|max:255',
        ]);

        ScheduleBlock::create([
            'is_recurring' => (bool) $validated['is_recurring'],

            'date'        => $validated['is_recurring'] ? null : $validated['date'],
            'day_of_week' => $validated['is_recurring'] ? (int) $validated['day_of_week'] : null,

            'start_time'  => $validated['start_time'],
            'end_time'    => $validated['end_time'],

            'reason'      => $validated['reason'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        return redirect()
            ->route('schedule.edit')
            ->with('success', 'Blokkade toegevoegd.');
    }

    public function destroyBlock($id)
    {
        $block = ScheduleBlock::findOrFail((int) $id);

        $block->delete();

        return redirect()
            ->route('schedule.edit')
            ->with('success', 'Blokkade verwijderd.');
    }
}
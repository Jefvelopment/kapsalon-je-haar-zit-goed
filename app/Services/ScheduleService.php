<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BusinessHour;
use App\Models\ScheduleBlock;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ScheduleService
{
    public function slotsForDate(Carbon|CarbonImmutable $date): Collection
    {
        $dayOfWeek = $date->dayOfWeek; // 0 = zondag ... 6 = zaterdag

        $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();

        if (!$businessHour || !$businessHour->is_open) {
            return collect();
        }

        $slotMinutes = $businessHour->slot_minutes;

        $start = $date->copy()->setTimeFromTimeString($businessHour->start_time->format('H:i'));
        $end   = $date->copy()->setTimeFromTimeString($businessHour->end_time->format('H:i'));

        $appointments = Appointment::with('treatments')
            ->whereDate('date', $date->toDateString())
            ->get();

        $blocks = ScheduleBlock::where(function ($query) use ($date, $dayOfWeek) {
            $query->where(function ($q) use ($date) {
                $q->where('is_recurring', false)->whereDate('date', $date->toDateString());
            })->orWhere(function ($q) use ($dayOfWeek) {
                $q->where('is_recurring', true)->where('day_of_week', $dayOfWeek);
            });
        })->get();

        $slots = collect();
        $cursor = $start->copy();

        while ($cursor->lt($end)) {
            $slotEnd = $cursor->copy()->addMinutes($slotMinutes);

            if ($slotEnd->gt($end)) {
                break;
            }

            $status = 'available';
            $matchedAppointment = null;

            foreach ($blocks as $block) {
                $blockStart = $cursor->copy()->setTimeFromTimeString($block->start_time->format('H:i'));
                $blockEnd   = $cursor->copy()->setTimeFromTimeString($block->end_time->format('H:i'));

                if ($cursor->lt($blockEnd) && $slotEnd->gt($blockStart)) {
                    $status = 'blocked';
                    break;
                }
            }

            if ($status !== 'blocked') {
                foreach ($appointments as $appointment) {
                    $apptStart = $cursor->copy()->setTimeFromTimeString($appointment->time->format('H:i'));
                    $durationMinutes = $appointment->treatments->sum('duration_minutes') ?: $slotMinutes;
                    $apptEnd = $apptStart->copy()->addMinutes($durationMinutes);

                    if ($cursor->lt($apptEnd) && $slotEnd->gt($apptStart)) {
                        $status = 'booked';
                        $matchedAppointment = $appointment;
                        break;
                    }
                }
            }

            $slots->push([
                'start'       => $cursor->format('H:i'),
                'end'         => $slotEnd->format('H:i'),
                'status'      => $status,
                'appointment' => $matchedAppointment,
            ]);

            $cursor = $slotEnd;
        }

        return $slots;
    }

    public function slotsForWeek(Carbon|CarbonImmutable $startOfWeek): Collection
    {
        $days = collect();

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $days->push([
                'date'  => $date,
                'slots' => $this->slotsForDate($date),
            ]);
        }

        return $days;
    }


    public function isRangeAvailable(Carbon|CarbonImmutable $date, string $startTime, int $durationMinutes): bool
    {
        $slots = $this->slotsForDate($date);

        $rangeStart = $date->copy()->setTimeFromTimeString($startTime);
        $rangeEnd   = $rangeStart->copy()->addMinutes($durationMinutes);

        foreach ($slots as $slot) {
            $slotStart = $date->copy()->setTimeFromTimeString($slot['start']);
            $slotEnd   = $date->copy()->setTimeFromTimeString($slot['end']);

            if ($slotStart->lt($rangeEnd) && $slotEnd->gt($rangeStart)) {
                if ($slot['status'] !== 'available') {
                    return false;
                }
            }
        }

        return true;
    }
}
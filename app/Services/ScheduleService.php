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
    /**
     * Bereken alle tijdsloten voor een specifieke datum.
     *
     * Geeft een Collection van slot-objecten terug:
     * [
     *   'start' => '09:00',
     *   'end'   => '09:30',
     *   'status' => 'available' | 'booked' | 'blocked',
     *   'appointment' => Appointment|null, // alleen gevuld als status 'booked' is
     * ]
     */
    public function slotsForDate(Carbon|CarbonImmutable $date, ?int $excludeAppointmentId = null): Collection
    {
        $dayOfWeek = $date->dayOfWeek; // 0 = zondag ... 6 = zaterdag

        $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();

        if (!$businessHour || !$businessHour->is_open) {
            return collect();
        }

        $slotMinutes = $businessHour->slot_minutes;

        $start = $date->copy()->setTimeFromTimeString($businessHour->start_time->format('H:i'));
        $end   = $date->copy()->setTimeFromTimeString($businessHour->end_time->format('H:i'));

        // Haal alle afspraken op voor deze dag, met treatments voor duur-berekening.
        // De afspraak die je momenteel bewerkt (excludeAppointmentId) wordt overgeslagen,
        // anders zou een afspraak altijd conflicteren met zichzelf.
        $appointments = Appointment::with('treatments')
            ->whereDate('date', $date->toDateString())
            ->when($excludeAppointmentId, fn($query) => $query->where('id', '!=', $excludeAppointmentId))
            ->get();

        // Haal alle relevante blokkades op (eenmalig op deze datum, of herhalend op deze weekdag).
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

            // Check of dit slot binnen een blokkade valt.
            foreach ($blocks as $block) {
                $blockStart = $cursor->copy()->setTimeFromTimeString($block->start_time->format('H:i'));
                $blockEnd   = $cursor->copy()->setTimeFromTimeString($block->end_time->format('H:i'));

                if ($cursor->lt($blockEnd) && $slotEnd->gt($blockStart)) {
                    $status = 'blocked';
                    break;
                }
            }

            // Check of dit slot overlapt met een bestaande afspraak.
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

    /**
     * Bereken sloten voor een hele week (7 dagen vanaf $startOfWeek).
     *
     * Geeft een Collection terug van ['date' => Carbon, 'slots' => Collection].
     */
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

    /**
     * Check of een specifiek tijdsblok (start + duur) volledig vrij is op een datum.
     * Gebruikt bij het daadwerkelijk opslaan van een nieuwe afspraak om race conditions
     * en dubbele boekingen te voorkomen.
     */
    public function isRangeAvailable(Carbon|CarbonImmutable $date, string $startTime, int $durationMinutes, ?int $excludeAppointmentId = null): bool
    {
        $slots = $this->slotsForDate($date, $excludeAppointmentId);

        $rangeStart = $date->copy()->setTimeFromTimeString($startTime);
        $rangeEnd   = $rangeStart->copy()->addMinutes($durationMinutes);

        foreach ($slots as $slot) {
            $slotStart = $date->copy()->setTimeFromTimeString($slot['start']);
            $slotEnd   = $date->copy()->setTimeFromTimeString($slot['end']);

            // Alleen sloten die overlappen met de gewenste range zijn relevant.
            if ($slotStart->lt($rangeEnd) && $slotEnd->gt($rangeStart)) {
                if ($slot['status'] !== 'available') {
                    return false;
                }
            }
        }

        return true;
    }
}
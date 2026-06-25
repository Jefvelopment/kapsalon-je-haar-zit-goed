<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Treatment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentTreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointment = Appointment::first();
        $treatment = Treatment::first();

        if ($appointment && $treatment) {
            $appointment->treatments()->attach($treatment->id);
        }
    }
}

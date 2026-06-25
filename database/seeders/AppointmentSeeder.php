<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use App\Enums\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $customer = User::where('email', 'user@test.com')->first();
        $employee = User::where('email', 'employee@test.com')->first();

        Appointment::create([
            'user_id'     => $customer->id,
            'employee_id' => $employee->id,
            'date'        => '2025-01-15',
            'time'        => '10:00:00',
        ]);

        Appointment::create([
            'user_id'     => $customer->id,
            'employee_id' => $employee->id,
            'date'        => '2025-02-20',
            'time'        => '14:00:00',
        ]);
    }
}
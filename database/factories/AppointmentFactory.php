<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dateTime = fake()->dateTimeBetween('+1 days', '+30 days');

        return [
            'user_id' => User::factory(),
            'date' => $dateTime->format('Y-m-d'),
            'time' => $dateTime->format('H:i:s'),
        ];
    }
}

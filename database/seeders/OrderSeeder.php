<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        Order::create([
            'user_id' => $user->id,
            'date' => '2023-11-11',
            'status' => 'completed',
            'price' => 1500,
        ]);
    }
}

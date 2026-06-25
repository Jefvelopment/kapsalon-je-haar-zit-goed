<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
User::create([
    'name' => 'Test User',
    'email' => 'user@test.com',
    'password' => bcrypt('test'),
    'address' => 'Teststraat 1, 1234 AB Teststad',
    'role' => Role::Customer,
]);

User::create([
    'name' => 'Owner User',
    'email' => 'owner@test.com',
    'password' => bcrypt('test'),
    'address' => 'Ownerstraat 1, 1234 AB Teststad',
    'role' => Role::Owner,
]);

User::create([
    'name' => 'Employee User',
    'email' => 'employee@test.com',
    'password' => bcrypt('test'),
    'address' => 'Employeestraat 1, 1234 AB Teststad',
    'role' => Role::Employee,
]);
    }
}
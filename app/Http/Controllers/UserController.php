<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'address'  => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'address'  => $validated['address'] ?? null,
            'password' => Hash::make($validated['password']),
            'role'     => Role::Employee,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Medewerker aangemaakt.');
    }

    public function edit($id)
    {
        $user = User::findOrFail((int) $id);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail((int) $id);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Medewerker bijgewerkt.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail((int) $id);

        $user->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Medewerker verwijderd.');
    }
}
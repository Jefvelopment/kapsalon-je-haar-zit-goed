<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function index()
    {
        $treatments = Treatment::all();

        return view('treatments.index', compact('treatments'));
    }

    public function view($id)
    {
        $treatment = Treatment::findOrFail((int) $id);

        return view('treatments.view', compact('treatment'));
    }

    public function create()
    {
        return view('treatments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        Treatment::create($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Behandeling aangemaakt.');
    }

    public function edit($id)
    {
        $treatment = Treatment::findOrFail((int) $id);

        return view('treatments.edit', compact('treatment'));
    }

    public function update(Request $request, $id)
    {
        $treatment = Treatment::findOrFail((int) $id);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $treatment->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Behandeling bijgewerkt.');
    }

    public function destroy($id)
    {
        $treatment = Treatment::findOrFail((int) $id);

        $treatment->delete();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Behandeling verwijderd.');
    }
}
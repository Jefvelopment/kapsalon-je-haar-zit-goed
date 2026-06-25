<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        <h1 class="text-3xl font-bold mb-8">Afspraak bewerken</h1>

        <form method="POST" action="{{ route('appointments.update', $appointment->id) }}" class="bg-white rounded-lg shadow p-6 max-w-lg">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block font-semibold mb-1">Datum</label>
                <input type="date" name="date" value="{{ $appointment->date->format('Y-m-d') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Tijd</label>
                <input type="time" name="time" value="{{ $appointment->time->format('H:i') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Medewerker</label>
                <select name="employee_id" class="w-full border rounded px-3 py-2">
                    <option value="">— Geen medewerker —</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $appointment->employee_id == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-1">Behandelingen</label>
                @foreach($treatments as $treatment)
                    <label class="flex items-center gap-2 mb-1">
                        <input type="checkbox" name="treatments[]" value="{{ $treatment->id }}" {{ $appointment->treatments->contains($treatment->id) ? 'checked' : '' }}>
                        {{ $treatment->name }}
                    </label>
                @endforeach
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition font-semibold">
                    Opslaan
                </button>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition font-semibold">
                    Annuleren
                </a>
            </div>

        </form>

    </div>
</x-base-layout>
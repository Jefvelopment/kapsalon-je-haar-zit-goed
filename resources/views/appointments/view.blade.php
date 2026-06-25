<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        <div class="max-w-lg bg-white rounded-lg shadow p-6">

            <h1 class="text-3xl font-bold mb-8">Afspraak details</h1>

            <div class="mb-4">
                <p class="text-gray-500 text-sm font-semibold">Klant</p>
                <p class="text-lg">{{ $appointment->user->name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-gray-500 text-sm font-semibold">Medewerker</p>
                <p class="text-lg">{{ $appointment->employee?->name ?? '—' }}</p>
            </div>

            <div class="mb-4">
                <p class="text-gray-500 text-sm font-semibold">Datum</p>
                <p class="text-lg">{{ $appointment->date->format('d-m-Y') }}</p>
            </div>

            <div class="mb-4">
                <p class="text-gray-500 text-sm font-semibold">Tijd</p>
                <p class="text-lg">{{ $appointment->time->format('H:i') }}</p>
            </div>

            <div class="mb-8">
                <p class="text-gray-500 text-sm font-semibold">Behandelingen</p>
                <p class="text-lg">{{ $appointment->treatments->pluck('name')->join(', ') ?: '—' }}</p>
            </div>

            <div class="flex gap-2">
                @if(Auth::user()->role === \App\Enums\Role::Owner || Auth::user()->role === \App\Enums\Role::Employee)
                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition font-semibold">
                        Bewerken
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition font-semibold">
                    Terug
                </a>
            </div>

        </div>

    </div>
</x-base-layout>
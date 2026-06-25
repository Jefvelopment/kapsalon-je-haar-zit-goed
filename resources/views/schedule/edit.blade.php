@php
    $dayNames = ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'];
@endphp

<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">Openingstijden &amp; blokkades</h1>
            <p class="text-gray-500 mt-1">Beheer het vaste weekschema en tijdelijke sluitingen</p>
        </div>

        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded border border-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Vast weekschema --}}
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-olive">Vast weekschema</h2>
        </div>

        <form method="POST" action="{{ route('schedule.business-hours.update') }}" class="bg-white rounded-lg shadow overflow-hidden mb-12">
            @csrf
            @method('PATCH')

            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Dag</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Open</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Vanaf</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Tot</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Slotlengte (min)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($dayNames as $dayIndex => $dayName)
                        @php $hour = $businessHours->get($dayIndex); @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium">
                                {{ $dayName }}
                                <input type="hidden" name="days[{{ $dayIndex }}][day_of_week]" value="{{ $dayIndex }}">
                            </td>
                            <td class="px-6 py-4">
                                <input type="checkbox" name="days[{{ $dayIndex }}][is_open]" value="1"
                                    {{ $hour?->is_open ? 'checked' : '' }} class="w-4 h-4">
                            </td>
                            <td class="px-6 py-4">
                                <input type="time" name="days[{{ $dayIndex }}][start_time]"
                                    value="{{ $hour?->start_time?->format('H:i') ?? '09:00' }}"
                                    class="border border-gray-200 rounded px-2 py-1">
                            </td>
                            <td class="px-6 py-4">
                                <input type="time" name="days[{{ $dayIndex }}][end_time]"
                                    value="{{ $hour?->end_time?->format('H:i') ?? '18:00' }}"
                                    class="border border-gray-200 rounded px-2 py-1">
                            </td>
                            <td class="px-6 py-4">
                                <input type="number" name="days[{{ $dayIndex }}][slot_minutes]" min="5" max="240" step="5"
                                    value="{{ $hour?->slot_minutes ?? 30 }}"
                                    class="border border-gray-200 rounded px-2 py-1 w-20">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <button type="submit" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                    Openingstijden opslaan
                </button>
            </div>
        </form>

        {{-- Blokkades --}}
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-olive">Blokkades</h2>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="POST" action="{{ route('schedule.blocks.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                @csrf

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
                    <select name="is_recurring" id="is_recurring" onchange="toggleBlockFields()" class="w-full border border-gray-200 rounded px-2 py-2 text-sm">
                        <option value="0">Eenmalig</option>
                        <option value="1">Elke week</option>
                    </select>
                </div>

                <div class="md:col-span-1" id="dateField">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Datum</label>
                    <input type="date" name="date" class="w-full border border-gray-200 rounded px-2 py-2 text-sm">
                </div>

                <div class="md:col-span-1 hidden" id="dayOfWeekField">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dag</label>
                    <select name="day_of_week" class="w-full border border-gray-200 rounded px-2 py-2 text-sm">
                        @foreach($dayNames as $dayIndex => $dayName)
                            <option value="{{ $dayIndex }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Vanaf</label>
                    <input type="time" name="start_time" required class="w-full border border-gray-200 rounded px-2 py-2 text-sm">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tot</label>
                    <input type="time" name="end_time" required class="w-full border border-gray-200 rounded px-2 py-2 text-sm">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Reden (optioneel)</label>
                    <input type="text" name="reason" maxlength="255" placeholder="Bijv. vakantie" class="w-full border border-gray-200 rounded px-2 py-2 text-sm">
                </div>

                <div class="md:col-span-6">
                    <button type="submit" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                        + Blokkade toevoegen
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Type</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Wanneer</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Tijd</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Reden</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Acties</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($blocks as $block)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                @if($block->is_recurring)
                                    <span class="px-2 py-1 bg-olive bg-opacity-10 text-olive rounded text-xs font-semibold">Elke week</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-semibold">Eenmalig</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $block->is_recurring ? $dayNames[$block->day_of_week] : $block->date->format('d-m-Y') }}
                            </td>
                            <td class="px-6 py-4">{{ $block->start_time->format('H:i') }} - {{ $block->end_time->format('H:i') }}</td>
                            <td class="px-6 py-4">{{ $block->reason ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('schedule.blocks.destroy', $block->id) }}" onsubmit="return confirm('Weet je zeker dat je deze blokkade wilt verwijderen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                        Verwijderen
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Geen blokkades ingesteld.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function toggleBlockFields() {
            const isRecurring = document.getElementById('is_recurring').value === '1';
            document.getElementById('dateField').classList.toggle('hidden', isRecurring);
            document.getElementById('dayOfWeekField').classList.toggle('hidden', !isRecurring);
        }
    </script>
</x-base-layout>
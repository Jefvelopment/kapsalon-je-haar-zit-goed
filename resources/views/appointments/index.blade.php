@php
    use App\Enums\Role;
    use Carbon\Carbon;

    $dayNames = ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'];
    $role = Auth::user()?->role;
@endphp

<x-base-layout>
    <div class="container mx-auto px-4 py-8 md:py-12">

        {{-- Header --}}
        <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">Afspraken</h1>
                <p class="text-gray-500 mt-1 text-sm md:text-base">
                    Week van {{ $startOfWeek->format('d-m-Y') }}
                    @if(!Auth::check())
                        — log in om een afspraak te boeken
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('appointments', ['week' => $prevWeek]) }}" class="flex-1 sm:flex-none text-center px-4 py-2 bg-white border border-gray-200 rounded shadow-sm hover:bg-gray-50 transition text-sm font-semibold">
                    ← Vorige week
                </a>
                <a href="{{ route('appointments', ['week' => $nextWeek]) }}" class="flex-1 sm:flex-none text-center px-4 py-2 bg-white border border-gray-200 rounded shadow-sm hover:bg-gray-50 transition text-sm font-semibold">
                    Volgende week →
                </a>
            </div>
        </div>

        {{-- Foutmelding (bv. slot niet meer beschikbaar) --}}
        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded border border-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Legenda --}}
        <div class="flex flex-wrap gap-4 sm:gap-6 mb-6 text-xs sm:text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-white border border-olive inline-block"></span> Vrij
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-gray-300 inline-block"></span> Bezet
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-gray-700 inline-block"></span> Gesloten / geblokkeerd
            </div>
        </div>

        {{-- MOBIEL: dag-voor-dag weergave --}}
        <div class="md:hidden">
            {{-- Dag-navigatie --}}
            <div class="flex items-center justify-between mb-4 bg-white rounded-lg shadow p-3">
                <button type="button" onclick="changeDay(-1)" id="prevDayBtn" class="px-3 py-2 text-olive font-bold text-lg">
                    ←
                </button>
                <div class="text-center">
                    <p class="font-semibold" id="mobileDayLabel">-</p>
                    <p class="text-xs text-gray-400" id="mobileDayDate">-</p>
                </div>
                <button type="button" onclick="changeDay(1)" id="nextDayBtn" class="px-3 py-2 text-olive font-bold text-lg">
                    →
                </button>
            </div>

            {{-- Eén dag-paneel per dag, getoggled via JS --}}
            @foreach($week as $dayIndex => $day)
                <div class="mobile-day-panel {{ $dayIndex === 0 ? '' : 'hidden' }} space-y-2" data-day-index="{{ $dayIndex }}">
                    @forelse($day['slots'] as $slot)
                        <div class="bg-white rounded-lg shadow p-3 flex items-center justify-between">
                            <span class="font-medium text-gray-600 text-sm">{{ $slot['start'] }} - {{ $slot['end'] }}</span>

                            @if($slot['status'] === 'available')
                                @if(Auth::check())
                                    <button type="button"
                                        onclick="openBookingModal('{{ $day['date']->toDateString() }}', '{{ $slot['start'] }}', '{{ $dayNames[$day['date']->dayOfWeek] }} {{ $day['date']->format('d-m-Y') }}')"
                                        class="px-4 py-1.5 bg-white border border-olive text-olive rounded hover:bg-olive hover:text-white transition text-xs font-semibold">
                                        Boeken
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="px-4 py-1.5 bg-white border border-olive text-olive rounded hover:bg-olive hover:text-white transition text-xs font-semibold">
                                        Inloggen
                                    </a>
                                @endif
                            @elseif($slot['status'] === 'booked')
                                @php $visible = $slot['visible_appointment'] ?? null; @endphp
                                <span class="px-4 py-1.5 bg-gray-300 text-gray-700 rounded text-xs font-semibold">
                                    {{ $visible ? $visible->user->name : 'Bezet' }}
                                </span>
                            @else
                                <span class="px-4 py-1.5 bg-gray-700 text-white rounded text-xs font-semibold">
                                    Gesloten
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow p-6 text-center text-gray-400 text-sm">
                            Geen openingstijden op deze dag.
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>

        {{-- TABLET/DESKTOP: week-tabel --}}
        <div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm border-collapse min-w-[800px]">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 w-24">Tijd</th>
                        @foreach($week as $day)
                            <th class="text-center px-2 py-3 font-semibold text-gray-600">
                                {{ $dayNames[$day['date']->dayOfWeek] }}<br>
                                <span class="text-xs text-gray-400">{{ $day['date']->format('d-m') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Verzamel alle unieke starttijden over de week, zodat elke rij dezelfde tijd toont.
                        $allTimes = $week->flatMap(fn($day) => $day['slots']->pluck('start'))->unique()->sort()->values();
                    @endphp

                    @forelse($allTimes as $time)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-2 text-gray-500 font-medium">{{ $time }}</td>
                            @foreach($week as $day)
                                @php
                                    $slot = $day['slots']->firstWhere('start', $time);
                                @endphp
                                <td class="px-2 py-2 text-center">
                                    @if(!$slot)
                                        {{-- Deze dag heeft geen slot op dit tijdstip (bv. korter open) --}}
                                        <span class="text-gray-300">—</span>
                                    @elseif($slot['status'] === 'available')
                                        @if(Auth::check())
                                            <button type="button"
                                                onclick="openBookingModal('{{ $day['date']->toDateString() }}', '{{ $slot['start'] }}', '{{ $dayNames[$day['date']->dayOfWeek] }} {{ $day['date']->format('d-m-Y') }}')"
                                                class="w-full px-2 py-1.5 bg-white border border-olive text-olive rounded hover:bg-olive hover:text-white transition text-xs font-semibold">
                                                {{ $slot['start'] }}
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}"
                                                class="w-full inline-block px-2 py-1.5 bg-white border border-olive text-olive rounded hover:bg-olive hover:text-white transition text-xs font-semibold">
                                                {{ $slot['start'] }}
                                            </a>
                                        @endif
                                    @elseif($slot['status'] === 'booked')
                                        @php $visible = $slot['visible_appointment'] ?? null; @endphp
                                        <div class="px-2 py-1.5 bg-gray-300 text-gray-700 rounded text-xs font-semibold" title="{{ $visible ? $visible->treatments->pluck('name')->join(', ') : 'Bezet' }}">
                                            @if($visible)
                                                {{ $visible->user->name }}
                                            @else
                                                Bezet
                                            @endif
                                        </div>
                                    @else
                                        {{-- blocked --}}
                                        <div class="px-2 py-1.5 bg-gray-700 text-white rounded text-xs font-semibold">
                                            Gesloten
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">Deze week zijn er geen openingstijden ingesteld.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($role === Role::Owner)
            <div class="mt-6">
                <a href="{{ route('schedule.edit') }}" class="inline-block w-full sm:w-auto text-center bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                    Openingstijden &amp; blokkades beheren
                </a>
            </div>
        @endif

    </div>

    {{-- Boekings-modal --}}
    @auth
        <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full max-h-[90vh] overflow-y-auto">
                <h2 class="text-xl font-bold mb-1 text-olive">Afspraak maken</h2>
                <p id="bookingModalSubtitle" class="text-sm text-gray-500 mb-4"></p>

                <form method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    <input type="hidden" name="date" id="bookingDate">
                    <input type="hidden" name="time" id="bookingTime">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Behandeling(en)</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded p-3">
                            @foreach(\App\Models\Treatment::all() as $treatment)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="treatments[]" value="{{ $treatment->id }}" class="w-4 h-4">
                                    <span class="text-sm">{{ $treatment->name }} <span class="text-gray-400">({{ $treatment->duration_minutes }} min, € {{ number_format($treatment->price, 2, ',', '.') }})</span></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Voorkeur medewerker (optioneel)</label>
                        <select name="employee_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm">
                            <option value="">Geen voorkeur</option>
                            @foreach(\App\Models\User::where('role', Role::Employee)->get() as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="closeBookingModal()" class="flex-1 px-3 py-2 bg-gray-200 rounded text-black hover:bg-gray-300 font-semibold">
                            Annuleren
                        </button>
                        <button type="submit" class="flex-1 px-3 py-2 bg-olive rounded text-white hover:bg-opacity-80 font-semibold">
                            Bevestigen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth

    <script>
        function openBookingModal(date, time, label) {
            document.getElementById('bookingDate').value = date;
            document.getElementById('bookingTime').value = time;
            document.getElementById('bookingModalSubtitle').textContent = label + ' om ' + time;
            document.getElementById('bookingModal').classList.remove('hidden');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        // Mobiele dag-voor-dag navigatie
        const dayLabels = @json($week->map(fn($day) => \Carbon\Carbon::parse($day['date'])->locale('nl')->isoFormat('dddd'))->values());
        const dayDates = @json($week->map(fn($day) => $day['date']->format('d-m-Y'))->values());
        const dayPanels = document.querySelectorAll('.mobile-day-panel');
        let currentDayIndex = 0;

        function showDay(index) {
            dayPanels.forEach(panel => {
                panel.classList.toggle('hidden', parseInt(panel.dataset.dayIndex) !== index);
            });
            document.getElementById('mobileDayLabel').textContent = dayLabels[index] ?? '-';
            document.getElementById('mobileDayDate').textContent = dayDates[index] ?? '-';
            document.getElementById('prevDayBtn').style.visibility = index === 0 ? 'hidden' : 'visible';
            document.getElementById('nextDayBtn').style.visibility = index === dayPanels.length - 1 ? 'hidden' : 'visible';
        }

        function changeDay(delta) {
            const newIndex = currentDayIndex + delta;
            if (newIndex < 0 || newIndex >= dayPanels.length) return;
            currentDayIndex = newIndex;
            showDay(currentDayIndex);
        }

        document.addEventListener('DOMContentLoaded', () => showDay(0));
    </script>
</x-base-layout>
@php use App\Enums\Role; @endphp

<x-base-layout>
    <div class="container mx-auto px-4 py-8 md:py-12">

        {{-- Header --}}
        <div class="mb-6 md:mb-8">
            <h1 class="text-2xl md:text-3xl font-bold">Dashboard</h1>
            <p class="text-gray-500 mt-1 text-sm md:text-base">Welkom terug, {{ Auth::user()->name }}</p>
        </div>

        {{-- Success melding --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Owner --}}
        @if(Auth::user()->role === Role::Owner)

            <div class="space-y-3">

                {{-- Statistieken (standaard open) --}}
                <details open class="bg-white rounded-lg shadow group">
                    <summary class="cursor-pointer list-none px-4 sm:px-6 py-4 flex justify-between items-center font-semibold text-olive">
                        <span>📊 Statistieken</span>
                        <span class="text-gray-400 transition-transform group-open:rotate-180">▾</span>
                    </summary>
                    <div class="px-4 sm:px-6 pb-6">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                                <p class="text-gray-500 text-xs sm:text-sm font-semibold">Totaal bezoekers</p>
                                <p class="text-2xl sm:text-4xl font-bold text-olive mt-2">{{ $totalViews }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                                <p class="text-gray-500 text-xs sm:text-sm font-semibold">Bezoekers vandaag</p>
                                <p class="text-2xl sm:text-4xl font-bold text-olive mt-2">{{ $todayViews }}</p>
                            </div>
                        </div>
                    </div>
                </details>

                {{-- Verkoopcijfers --}}
                <details class="bg-white rounded-lg shadow group">
                    <summary class="cursor-pointer list-none px-4 sm:px-6 py-4 flex justify-between items-center font-semibold text-olive">
                        <span>💶 Verkoopcijfers</span>
                        <span class="text-gray-400 transition-transform group-open:rotate-180">▾</span>
                    </summary>
                    <div class="px-4 sm:px-6 pb-6">
                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-2 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Van</label>
                                <input type="date" name="from" value="{{ $salesPeriodStart->toDateString() }}" class="border border-gray-200 rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Tot</label>
                                <input type="date" name="to" value="{{ $salesPeriodEnd->toDateString() }}" class="border border-gray-200 rounded px-2 py-1.5 text-sm">
                            </div>
                            <button type="submit" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                                Filteren
                            </button>
                        </form>

                        <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                            <p class="text-gray-500 text-xs sm:text-sm font-semibold mb-1">Totale omzet in deze periode</p>
                            <p class="text-2xl sm:text-3xl font-bold text-olive mb-6">€ {{ number_format($salesPeriodTotal, 2, ',', '.') }}</p>
                            <div class="overflow-x-auto">
                                <canvas id="salesChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                </details>

                {{-- Bestellingen --}}
                <details class="bg-white rounded-lg shadow group">
                    <summary class="cursor-pointer list-none px-4 sm:px-6 py-4 flex justify-between items-center font-semibold text-olive">
                        <span>📦 Bestellingen <span class="text-gray-400 font-normal text-sm">({{ $allOrders->count() }})</span></span>
                        <span class="text-gray-400 transition-transform group-open:rotate-180">▾</span>
                    </summary>
                    <div class="px-4 sm:px-6 pb-6">
                        {{-- Mobiel: kaarten --}}
                        <div class="md:hidden space-y-3">
                            @forelse($allOrders as $order)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-semibold text-sm">#{{ $order->id }} — {{ $order->customerName() }}</p>
                                            @if(!$order->user_id)
                                                <span class="inline-block mt-1 px-2 py-0.5 bg-gray-200 text-gray-500 rounded text-xs">Gast</span>
                                            @endif
                                            <p class="text-xs text-gray-400">{{ $order->customerEmail() }}</p>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $order->date->format('d-m-Y') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $order->products->map(fn($p) => $p->name . ' (' . $p->pivot->quantity . 'x)')->join(', ') }}
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-olive">€ {{ number_format($order->price / 100, 2, ',', '.') }}</span>
                                        <form method="POST" action="{{ route('orders.update-status', $order->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="border border-gray-200 rounded px-2 py-1.5 text-xs">
                                                @foreach(\App\Enums\OrderStatus::cases() as $status)
                                                    <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                        {{ $status->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-400">Geen bestellingen gevonden.</div>
                            @endforelse
                        </div>

                        {{-- Desktop/tablet: tabel --}}
                        <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">#</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Klant</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Datum</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Producten</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Totaal</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($allOrders as $order)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 text-gray-400">#{{ $order->id }}</td>
                                            <td class="px-4 py-3">
                                                {{ $order->customerName() }}
                                                @if(!$order->user_id)
                                                    <span class="ml-1 px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">Gast</span>
                                                @endif
                                                <div class="text-xs text-gray-400">{{ $order->customerEmail() }}</div>
                                            </td>
                                            <td class="px-4 py-3">{{ $order->date->format('d-m-Y') }}</td>
                                            <td class="px-4 py-3">
                                                {{ $order->products->map(fn($p) => $p->name . ' (' . $p->pivot->quantity . 'x)')->join(', ') }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold">€ {{ number_format($order->price / 100, 2, ',', '.') }}</td>
                                            <td class="px-4 py-3">
                                                <form method="POST" action="{{ route('orders.update-status', $order->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()" class="border border-gray-200 rounded px-2 py-1 text-xs">
                                                        @foreach(\App\Enums\OrderStatus::cases() as $status)
                                                            <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                                {{ $status->label() }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Geen bestellingen gevonden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>

                {{-- Bezoeken per klant --}}
                <details class="bg-white rounded-lg shadow group">
                    <summary class="cursor-pointer list-none px-4 sm:px-6 py-4 flex justify-between items-center font-semibold text-olive">
                        <span>👥 Bezoeken per klant <span class="text-gray-400 font-normal text-sm">({{ $customerVisits->count() }})</span></span>
                        <span class="text-gray-400 transition-transform group-open:rotate-180">▾</span>
                    </summary>
                    <div class="px-4 sm:px-6 pb-6">
                        {{-- Mobiel: kaarten --}}
                        <div class="md:hidden space-y-3">
                            @forelse($customerVisits as $customer)
                                <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold text-sm">{{ $customer->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $customer->email }}</p>
                                    </div>
                                    <span class="font-bold text-olive">{{ $customer->page_views_count }}</span>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-400">Geen klanten gevonden.</div>
                            @endforelse
                        </div>

                        {{-- Desktop/tablet: tabel --}}
                        <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Klant</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">E-mail</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Aantal bezoeken</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($customerVisits as $customer)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3">{{ $customer->name }}</td>
                                            <td class="px-4 py-3">{{ $customer->email }}</td>
                                            <td class="px-4 py-3 font-semibold text-olive">{{ $customer->page_views_count }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Geen klanten gevonden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>

                {{-- Afspraken --}}
                <details class="bg-white rounded-lg shadow group">
                    <summary class="cursor-pointer list-none px-4 sm:px-6 py-4 flex justify-between items-center font-semibold text-olive">
                        <span>📅 Alle afspraken <span class="text-gray-400 font-normal text-sm">({{ $appointments->count() }})</span></span>
                        <span class="text-gray-400 transition-transform group-open:rotate-180">▾</span>
                    </summary>
                    <div class="px-4 sm:px-6 pb-6">
                        <div class="mb-4">
                            <a href="{{ route('appointments.create') }}" class="inline-block bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                                + Nieuwe afspraak
                            </a>
                        </div>

                        {{-- Mobiel: kaarten --}}
                        <div class="md:hidden space-y-3">
                            @forelse($appointments as $appointment)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="font-semibold text-sm">{{ $appointment->user->name }}</p>
                                        <span class="text-xs text-gray-500">{{ $appointment->date->format('d-m-Y') }} {{ $appointment->time->format('H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-1">Medewerker: {{ $appointment->employee?->name ?? '—' }}</p>
                                    <p class="text-sm text-gray-600 mb-3">{{ $appointment->treatments->pluck('name')->join(', ') ?: '—' }}</p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('appointments.view', $appointment->id) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-xs font-semibold">
                                            Bekijken
                                        </a>
                                        <a href="{{ route('appointments.edit', $appointment->id) }}" class="flex-1 text-center px-3 py-1.5 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                            Bewerken
                                        </a>
                                        <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze afspraak wilt verwijderen?')" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                                Verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-400">Geen afspraken gevonden.</div>
                            @endforelse
                        </div>

                        {{-- Desktop/tablet: tabel --}}
                        <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Klant</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Medewerker</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Datum</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Tijd</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Behandelingen</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Acties</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($appointments as $appointment)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3">{{ $appointment->user->name }}</td>
                                            <td class="px-4 py-3">{{ $appointment->employee?->name ?? '—' }}</td>
                                            <td class="px-4 py-3">{{ $appointment->date->format('d-m-Y') }}</td>
                                            <td class="px-4 py-3">{{ $appointment->time->format('H:i') }}</td>
                                            <td class="px-4 py-3">{{ $appointment->treatments->pluck('name')->join(', ') ?: '—' }}</td>
                                            <td class="px-4 py-3 flex gap-2">
                                                <a href="{{ route('appointments.view', $appointment->id) }}" class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-xs font-semibold">
                                                    Bekijken
                                                </a>
                                                <a href="{{ route('appointments.edit', $appointment->id) }}" class="px-3 py-1 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                                    Bewerken
                                                </a>
                                                <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze afspraak wilt verwijderen?')">
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
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Geen afspraken gevonden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>

                {{-- Behandelingen --}}
                <details class="bg-white rounded-lg shadow group">
                    <summary class="cursor-pointer list-none px-4 sm:px-6 py-4 flex justify-between items-center font-semibold text-olive">
                        <span>✂️ Behandelingen <span class="text-gray-400 font-normal text-sm">({{ $treatments->count() }})</span></span>
                        <span class="text-gray-400 transition-transform group-open:rotate-180">▾</span>
                    </summary>
                    <div class="px-4 sm:px-6 pb-6">
                        <div class="mb-4">
                            <a href="{{ route('treatments.create') }}" class="inline-block bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                                + Nieuwe behandeling
                            </a>
                        </div>

                        {{-- Mobiel: kaarten --}}
                        <div class="md:hidden space-y-3">
                            @forelse($treatments as $treatment)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <p class="font-semibold text-sm">{{ $treatment->name }}</p>
                                        <p class="font-bold text-olive">€ {{ number_format($treatment->price, 2, ',', '.') }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('treatments.edit', $treatment->id) }}" class="flex-1 text-center px-3 py-1.5 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                            Bewerken
                                        </a>
                                        <form method="POST" action="{{ route('treatments.destroy', $treatment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze behandeling wilt verwijderen?')" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                                Verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-400">Geen behandelingen gevonden.</div>
                            @endforelse
                        </div>

                        {{-- Desktop/tablet: tabel --}}
                        <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Naam</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Prijs</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Acties</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($treatments as $treatment)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3">{{ $treatment->name }}</td>
                                            <td class="px-4 py-3">€ {{ number_format($treatment->price, 2, ',', '.') }}</td>
                                            <td class="px-4 py-3 flex gap-2">
                                                <a href="{{ route('treatments.edit', $treatment->id) }}" class="px-3 py-1 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                                    Bewerken
                                                </a>
                                                <form method="POST" action="{{ route('treatments.destroy', $treatment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze behandeling wilt verwijderen?')">
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
                                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Geen behandelingen gevonden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>

                {{-- Medewerkers --}}
                <details class="bg-white rounded-lg shadow group">
                    <summary class="cursor-pointer list-none px-4 sm:px-6 py-4 flex justify-between items-center font-semibold text-olive">
                        <span>👩‍🔧 Medewerkers <span class="text-gray-400 font-normal text-sm">({{ $employees->count() }})</span></span>
                        <span class="text-gray-400 transition-transform group-open:rotate-180">▾</span>
                    </summary>
                    <div class="px-4 sm:px-6 pb-6">
                        <div class="mb-4">
                            <a href="{{ route('users.create') }}" class="inline-block bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                                + Nieuwe medewerker
                            </a>
                        </div>

                        {{-- Mobiel: kaarten --}}
                        <div class="md:hidden space-y-3">
                            @forelse($employees as $employee)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="font-semibold text-sm">{{ $employee->name }}</p>
                                    <p class="text-xs text-gray-400 mb-1">{{ $employee->email }}</p>
                                    <p class="text-xs text-gray-400 mb-3">{{ $employee->address ?? '—' }}</p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('users.edit', $employee->id) }}" class="flex-1 text-center px-3 py-1.5 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                            Bewerken
                                        </a>
                                        <form method="POST" action="{{ route('users.destroy', $employee->id) }}" onsubmit="return confirm('Weet je zeker dat je deze medewerker wilt verwijderen?')" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                                Verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50 rounded-lg p-8 text-center text-gray-400">Geen medewerkers gevonden.</div>
                            @endforelse
                        </div>

                        {{-- Desktop/tablet: tabel --}}
                        <div class="hidden md:block overflow-x-auto rounded-lg border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Naam</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">E-mail</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Adres</th>
                                        <th class="text-left px-4 py-3 font-semibold text-gray-600">Acties</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($employees as $employee)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3">{{ $employee->name }}</td>
                                            <td class="px-4 py-3">{{ $employee->email }}</td>
                                            <td class="px-4 py-3">{{ $employee->address ?? '—' }}</td>
                                            <td class="px-4 py-3 flex gap-2">
                                                <a href="{{ route('users.edit', $employee->id) }}" class="px-3 py-1 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                                    Bewerken
                                                </a>
                                                <form method="POST" action="{{ route('users.destroy', $employee->id) }}" onsubmit="return confirm('Weet je zeker dat je deze medewerker wilt verwijderen?')">
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
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">Geen medewerkers gevonden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>

            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
            <script>
                new Chart(document.getElementById('salesChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($salesChartLabels),
                        datasets: [{
                            label: 'Omzet (€)',
                            data: @json($salesChartTotals),
                            backgroundColor: '#6B8E23',
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '€ ' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            </script>

        {{-- Employee --}}
        @elseif(Auth::user()->role === Role::Employee)
            <div class="mb-4">
                <h2 class="text-lg md:text-xl font-semibold text-olive">Mijn afspraken</h2>
            </div>

            {{-- Mobiel: kaarten --}}
            <div class="md:hidden space-y-3">
                @forelse($appointments as $appointment)
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex justify-between items-start mb-2">
                            <p class="font-semibold text-sm">{{ $appointment->user->name }}</p>
                            <span class="text-xs text-gray-500">{{ $appointment->date->format('d-m-Y') }} {{ $appointment->time->format('H:i') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ $appointment->treatments->pluck('name')->join(', ') ?: '—' }}</p>
                        <div class="flex gap-2">
                            <a href="{{ route('appointments.view', $appointment->id) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-xs font-semibold">
                                Bekijken
                            </a>
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="flex-1 text-center px-3 py-1.5 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                Bewerken
                            </a>
                            @if($appointment->employee_id === Auth::user()->id)
                                <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze afspraak wilt verwijderen?')" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-3 py-1.5 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                        Verwijderen
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">Geen afspraken gevonden.</div>
                @endforelse
            </div>

            {{-- Desktop/tablet: tabel --}}
            <div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Klant</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Datum</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Tijd</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Behandelingen</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">{{ $appointment->user->name }}</td>
                                <td class="px-6 py-4">{{ $appointment->date->format('d-m-Y') }}</td>
                                <td class="px-6 py-4">{{ $appointment->time->format('H:i') }}</td>
                                <td class="px-6 py-4">{{ $appointment->treatments->pluck('name')->join(', ') ?: '—' }}</td>
                                <td class="px-6 py-4 flex gap-2">
                                    <a href="{{ route('appointments.view', $appointment->id) }}" class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-xs font-semibold">
                                        Bekijken
                                    </a>
                                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="px-3 py-1 bg-olive text-white rounded hover:bg-opacity-80 transition text-xs font-semibold">
                                        Bewerken
                                    </a>
                                    @if($appointment->employee_id === Auth::user()->id)
                                        <form method="POST" action="{{ route('appointments.destroy', $appointment->id) }}" onsubmit="return confirm('Weet je zeker dat je deze afspraak wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                                Verwijderen
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Geen afspraken gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        {{-- Customer --}}
        @else
            <div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <h2 class="text-lg md:text-xl font-semibold text-olive">Mijn afspraken</h2>
                <a href="{{ route('appointments.create') }}" class="text-center bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                    + Nieuwe afspraak
                </a>
            </div>

            {{-- Mobiel: kaarten --}}
            <div class="md:hidden space-y-3">
                @forelse($appointments as $appointment)
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-sm font-semibold">{{ $appointment->date->format('d-m-Y') }} — {{ $appointment->time->format('H:i') }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">Medewerker: {{ $appointment->employee?->name ?? '—' }}</p>
                        <p class="text-sm text-gray-600">{{ $appointment->treatments->pluck('name')->join(', ') ?: '—' }}</p>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">Geen afspraken gevonden.</div>
                @endforelse
            </div>

            {{-- Desktop/tablet: tabel --}}
            <div class="hidden md:block bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Datum</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Tijd</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Medewerker</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Behandelingen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($appointments as $appointment)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">{{ $appointment->date->format('d-m-Y') }}</td>
                                <td class="px-6 py-4">{{ $appointment->time->format('H:i') }}</td>
                                <td class="px-6 py-4">{{ $appointment->employee?->name ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $appointment->treatments->pluck('name')->join(', ') ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">Geen afspraken gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</x-base-layout>

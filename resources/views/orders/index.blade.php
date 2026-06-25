@php use App\Enums\OrderStatus; @endphp

<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">Mijn bestellingen</h1>
            <p class="text-gray-500 mt-1">Overzicht van je bestelde producten</p>
        </div>

        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Datum</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Producten</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Totaal</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ $order->date->format('d-m-Y') }}</td>
                            <td class="px-6 py-4">
                                {{ $order->products->map(fn($p) => $p->name . ' (' . $p->pivot->quantity . 'x)')->join(', ') }}
                            </td>
                            <td class="px-6 py-4 font-semibold">€ {{ number_format($order->price / 100, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($order->status) {
                                        OrderStatus::InProgress => 'bg-gray-100 text-gray-600',
                                        OrderStatus::Ready => 'bg-olive bg-opacity-10 text-olive',
                                        OrderStatus::Completed => 'bg-green-100 text-green-700',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColor }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">Je hebt nog geen bestellingen geplaatst.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-base-layout>
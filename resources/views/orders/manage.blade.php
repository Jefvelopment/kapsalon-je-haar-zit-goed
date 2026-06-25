@php use App\Enums\OrderStatus; @endphp

<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">Bestellingen beheren</h1>
            <p class="text-gray-500 mt-1">Alle bestellingen van klanten en gasten</p>
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
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">#</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Klant</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Datum</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Producten</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Totaal</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-400">#{{ $order->id }}</td>
                            <td class="px-6 py-4">
                                {{ $order->customerName() }}
                                @if(!$order->user_id)
                                    <span class="ml-1 px-2 py-0.5 bg-gray-100 text-gray-500 rounded text-xs">Gast</span>
                                @endif
                                <div class="text-xs text-gray-400">{{ $order->customerEmail() }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $order->date->format('d-m-Y') }}</td>
                            <td class="px-6 py-4">
                                {{ $order->products->map(fn($p) => $p->name . ' (' . $p->pivot->quantity . 'x)')->join(', ') }}
                            </td>
                            <td class="px-6 py-4 font-semibold">€ {{ number_format($order->price / 100, 2, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('orders.update-status', $order->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="border border-gray-200 rounded px-2 py-1 text-xs">
                                        @foreach(OrderStatus::cases() as $status)
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
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">Geen bestellingen gevonden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-base-layout>
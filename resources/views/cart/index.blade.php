<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">Winkelmandje</h1>
            <p class="text-gray-500 mt-1">Bestel online, haal op en betaal in de winkel</p>
        </div>

        {{-- Success melding --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Foutmelding (bv. niet genoeg voorraad) --}}
        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded border border-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if($items->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400">
                <p class="mb-4">Je mandje is leeg.</p>
                <a href="{{ route('products') }}" class="inline-block px-4 py-2 bg-olive text-white rounded hover:bg-opacity-80 transition text-sm font-semibold">
                    Bekijk producten
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Product</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Prijs</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Aantal</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Subtotaal</th>
                            <th class="text-left px-6 py-3 font-semibold text-gray-600">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">{{ $item['product']->name }}</td>
                                <td class="px-6 py-4">€ {{ number_format($item['product']->price / 100, 2, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('cart.update', $item['product']->id) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}"
                                            class="w-16 border border-gray-200 rounded px-2 py-1">
                                        <button type="submit" class="text-xs text-olive hover:underline font-semibold">
                                            Bijwerken
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 font-semibold">€ {{ number_format($item['subtotal'] / 100, 2, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}" onsubmit="return confirm('Product uit mandje verwijderen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                            Verwijderen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold">Totaal</td>
                            <td class="px-6 py-4 font-bold text-olive">€ {{ number_format($total / 100, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('products') }}" class="text-olive hover:underline text-sm">← Verder winkelen</a>
                <a href="{{ route('orders.create') }}" class="bg-olive text-white px-6 py-3 rounded hover:bg-opacity-80 transition font-semibold">
                    Afrekenen
                </a>
            </div>
        @endif

    </div>
</x-base-layout>
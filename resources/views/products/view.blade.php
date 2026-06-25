@php use App\Enums\Role; @endphp

<x-base-layout>
    <div class="container mx-auto px-4 py-12 max-w-3xl">

        <div class="mb-6">
            <a href="{{ route('products') }}" class="text-olive hover:underline text-sm">
                ← Terug naar producten
            </a>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded border border-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Success --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow border border-olive border-opacity-20 overflow-hidden">

            <img src="{{ $product->imageUrl() }}"
                 alt="{{ $product->name }}"
                 class="w-full h-64 object-cover">

            <div class="p-8">

                <p class="text-xs text-gray-400 uppercase font-semibold mb-2">
                    {{ $product->category ?? 'Overig' }}
                </p>

                <h1 class="text-2xl font-bold mb-2">
                    {{ $product->name }}
                </h1>

                <p class="text-3xl font-bold text-olive mb-4">
                    € {{ number_format($product->price / 100, 2, ',', '.') }}
                </p>

                @if($product->stock > 0)
                    <p class="text-sm text-gray-500 mb-6">
                        Op voorraad: {{ $product->stock }} stuks
                    </p>
                @else
                    <p class="text-sm text-red-500 mb-6 font-semibold">
                        Niet op voorraad
                    </p>
                @endif

                @if($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="flex items-end gap-3 mb-6">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                Aantal
                            </label>
                            <input type="number"
                                   name="quantity"
                                   value="1"
                                   min="1"
                                   max="{{ $product->stock }}"
                                   class="border border-gray-200 rounded px-3 py-2 w-24">
                        </div>

                        <button type="submit"
                                class="bg-olive text-white px-6 py-2 rounded hover:bg-opacity-80 transition font-semibold">
                            Toevoegen
                        </button>
                    </form>
                @endif

                <p class="text-xs text-gray-400">
                    Bestellen en ophalen in de winkel
                </p>

                @auth
                    @if(Auth::user()->role === Role::Owner)
                        <div class="flex gap-2 mt-8 pt-6 border-t border-gray-100">
                            <a href="{{ route('products.edit', $product->id) }}"
                               class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-sm font-semibold">
                                Bewerken
                            </a>

                            <form method="POST"
                                  action="{{ route('products.destroy', $product->id) }}"
                                  onsubmit="return confirm('Product verwijderen?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm font-semibold">
                                    Verwijderen
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth

            </div>
        </div>

    </div>
</x-base-layout>
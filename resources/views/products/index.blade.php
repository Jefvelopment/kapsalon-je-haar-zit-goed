@php use App\Enums\Role; @endphp

<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        {{-- Header --}}
        <div class="mb-8 flex flex-wrap justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold">Producten</h1>
                <p class="text-gray-500 mt-1">Bestel online, haal op en betaal in de winkel</p>
            </div>
            <div class="flex gap-2">
                @auth
                    @if(Auth::user()->role === Role::Owner)
                        <a href="{{ route('products.create') }}" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                            + Nieuw product
                        </a>
                    @endif
                @endauth
                <a href="{{ route('cart.index') }}" class="bg-white border border-olive text-olive px-4 py-2 rounded hover:bg-olive hover:text-white transition text-sm font-semibold">
                    🛒 Mandje
                </a>
            </div>
        </div>

        {{-- Success melding --}}
        @if(session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        {{-- Aanbevolen voor jou --}}
        @if($recommended->isNotEmpty())
            <div class="mb-12">
                <h2 class="text-xl font-semibold text-olive mb-4">Aanbevolen voor jou</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($recommended as $product)
                        <div class="bg-white rounded-lg shadow p-4 border border-olive border-opacity-30">
                            <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded mb-3">
                            <p class="text-xs text-gray-400 uppercase font-semibold mb-1">{{ $product->category ?? 'Product' }}</p>
                            <h3 class="font-semibold mb-2">{{ $product->name }}</h3>
                            <p class="text-olive font-bold mb-3">€ {{ number_format($product->price / 100, 2, ',', '.') }}</p>
                            <a href="{{ route('products.view', $product->id) }}" class="block text-center px-3 py-2 bg-olive text-white rounded hover:bg-opacity-80 transition text-sm font-semibold">
                                Bekijken
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Alle producten --}}
        <h2 class="text-xl font-semibold text-olive mb-4">Alle producten</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                <div class="bg-white rounded-lg shadow p-4">
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-full h-32 object-cover rounded mb-3">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-1">{{ $product->category ?? 'Overig' }}</p>
                    <h3 class="font-semibold mb-1">{{ $product->name }}</h3>
                    <p class="text-olive font-bold mb-1">€ {{ number_format($product->price / 100, 2, ',', '.') }}</p>

                    @if($product->stock > 0)
                        <p class="text-xs text-gray-500 mb-3">Op voorraad: {{ $product->stock }}</p>
                    @else
                        <p class="text-xs text-red-500 mb-3">Niet op voorraad</p>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('products.view', $product->id) }}" class="flex-1 text-center px-3 py-2 bg-olive text-white rounded hover:bg-opacity-80 transition text-sm font-semibold">
                            Bekijken
                        </a>
                    </div>

                    @auth
                        @if(Auth::user()->role === Role::Owner)
                            <div class="flex gap-2 mt-2">
                                <a href="{{ route('products.edit', $product->id) }}" class="flex-1 text-center px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 transition text-xs font-semibold">
                                    Bewerken
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="flex-1" onsubmit="return confirm('Weet je zeker dat je dit product wilt verwijderen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-xs font-semibold">
                                        Verwijderen
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            @empty
                <p class="col-span-full text-center text-gray-400 py-8">Geen producten gevonden.</p>
            @endforelse
        </div>

    </div>
</x-base-layout>
<x-base-layout>
    <div class="container mx-auto px-4 py-12 max-w-xl">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">Product bewerken</h1>
            <p class="text-gray-500 mt-1">{{ $product->name }}</p>
        </div>

        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded border border-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Naam</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="255"
                    class="w-full border border-gray-200 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Categorie</label>
                <input type="text" name="category" value="{{ old('category', $product->category) }}" maxlength="255"
                    placeholder="Bijv. Shampoo, Styling, Verzorging"
                    class="w-full border border-gray-200 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Prijs (in centen)</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0" step="1"
                    class="w-full border border-gray-200 rounded px-3 py-2">
                <p class="text-xs text-gray-400 mt-1">Vul de prijs in hele centen in (€ 12,50 = 1250). Huidige prijs: € {{ number_format($product->price / 100, 2, ',', '.') }}</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Voorraad</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0" step="1"
                    class="w-full border border-gray-200 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Productfoto</label>

                <div class="mb-2">
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded border border-gray-200">
                </div>

                <input type="file" name="image" accept="image/*"
                    class="w-full border border-gray-200 rounded px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Laat leeg om de huidige foto te behouden. Max 4MB.</p>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('products') }}" class="flex-1 text-center px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition font-semibold">
                    Annuleren
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-olive text-white rounded hover:bg-opacity-80 transition font-semibold">
                    Opslaan
                </button>
            </div>
        </form>

    </div>
</x-base-layout>
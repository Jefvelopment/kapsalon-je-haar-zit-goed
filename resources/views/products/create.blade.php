<x-base-layout>
    <div class="container mx-auto px-4 py-12 max-w-xl">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">Nieuw product</h1>
            <p class="text-gray-500 mt-1">Voeg een nieuw product toe aan de webshop</p>
        </div>

        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded border border-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Naam</label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="255"
                    class="w-full border border-gray-200 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Categorie</label>
                <input type="text" name="category" value="{{ old('category') }}" maxlength="255"
                    placeholder="Bijv. Shampoo, Styling, Verzorging"
                    class="w-full border border-gray-200 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Prijs (in centen)</label>
                <input type="number" name="price" value="{{ old('price') }}" required min="0" step="1"
                    placeholder="Bijv. 1250 voor € 12,50"
                    class="w-full border border-gray-200 rounded px-3 py-2">
                <p class="text-xs text-gray-400 mt-1">Vul de prijs in hele centen in (€ 12,50 = 1250).</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Voorraad</label>
                <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0" step="1"
                    class="w-full border border-gray-200 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Productfoto</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full border border-gray-200 rounded px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">Optioneel. Zonder foto wordt een standaardafbeelding getoond. Max 4MB.</p>
            </div>

            <div class="flex gap-2 pt-2">
                <a href="{{ route('products') }}" class="flex-1 text-center px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition font-semibold">
                    Annuleren
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-olive text-white rounded hover:bg-opacity-80 transition font-semibold">
                    Product toevoegen
                </button>
            </div>
        </form>

    </div>
</x-base-layout>
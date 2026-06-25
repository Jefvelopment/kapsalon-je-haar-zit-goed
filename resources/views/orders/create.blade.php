<x-base-layout>
    <div class="container mx-auto px-4 py-12 max-w-xl">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">Afrekenen</h1>
            <p class="text-gray-500 mt-1">Je betaalt en haalt je bestelling op in de winkel</p>
        </div>

        @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded border border-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('orders.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf

            @auth
                <div class="px-4 py-3 bg-gray-50 rounded border border-gray-200 text-sm">
                    Je bestelt als <span class="font-semibold">{{ Auth::user()->name }}</span> ({{ Auth::user()->email }}).
                </div>
            @else
                <p class="text-sm text-gray-500">Vul je gegevens in zodat we je kunnen bereiken zodra je bestelling gereed is.</p>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Naam</label>
                    <input type="text" name="guest_name" value="{{ old('guest_name') }}" required maxlength="255"
                        class="w-full border border-gray-200 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-mailadres</label>
                    <input type="email" name="guest_email" value="{{ old('guest_email') }}" required maxlength="255"
                        class="w-full border border-gray-200 rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telefoonnummer</label>
                    <input type="tel" name="guest_phone" value="{{ old('guest_phone') }}" required maxlength="30"
                        class="w-full border border-gray-200 rounded px-3 py-2">
                </div>

                <div class="px-4 py-3 bg-gray-50 rounded border border-gray-200 text-sm text-gray-600">
                    Liever een account? <a href="{{ route('register') }}" class="text-olive hover:underline font-semibold">Account aanmaken</a>
                    of <a href="{{ route('login') }}" class="text-olive hover:underline font-semibold">inloggen</a>.
                </div>
            @endauth

            <div class="flex gap-2 pt-2">
                <a href="{{ route('cart.index') }}" class="flex-1 text-center px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition font-semibold">
                    Terug naar mandje
                </a>
                <button type="submit" class="flex-1 bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition font-semibold">
                    Bestelling plaatsen
                </button>
            </div>
        </form>

    </div>
</x-base-layout>
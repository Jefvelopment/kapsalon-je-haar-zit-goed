<x-base-layout>
    <div class="container mx-auto px-4 py-12">

        <h1 class="text-3xl font-bold mb-8">Medewerker aanmaken</h1>

        <form method="POST" action="{{ route('users.store') }}" class="bg-white rounded-lg shadow p-6 max-w-lg">
            @csrf

            <div class="mb-4">
                <label class="block font-semibold mb-1">Naam</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">E-mail</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Adres</label>
                <input type="text" name="address" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Wachtwoord</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-6">
                <label class="block font-semibold mb-1">Wachtwoord bevestigen</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition font-semibold">
                    Aanmaken
                </button>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition font-semibold">
                    Annuleren
                </a>
            </div>

        </form>

    </div>
</x-base-layout>
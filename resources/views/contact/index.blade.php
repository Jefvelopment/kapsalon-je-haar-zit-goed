<x-base-layout>
    <div class="max-w-2xl mx-auto py-12 px-4">
        <h1 class="text-4xl font-bold text-olive mb-2">Contact</h1>
        <p class="text-gray-600 mb-8">Heeft u een vraag? Vul het formulier in en we nemen snel contact met u op.</p>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
            @csrf

            <div>
                <label for="inquiry_type" class="block text-sm font-semibold text-black mb-2">Waarover wilt u ons contacteren?</label>
                <select id="inquiry_type" name="inquiry_type" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">
                    <option value="">-- Selecteer een optie --</option>
                    <option value="treatment" {{ old('inquiry_type') == 'treatment' ? 'selected' : '' }}>Behandeling</option>
                    <option value="product" {{ old('inquiry_type') == 'product' ? 'selected' : '' }}>Product</option>
                    <option value="other" {{ old('inquiry_type') == 'other' ? 'selected' : '' }}>Iets anders</option>
                </select>
            </div>

            <div id="treatment_section" class="hidden">
                <label for="treatment_id" class="block text-sm font-semibold text-black mb-2">Selecteer een behandeling</label>
                <select id="treatment_id" name="treatment_id" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">
                    <option value="">-- Selecteer een behandeling --</option>
                    @foreach ($treatments as $treatment)
                        <option value="{{ $treatment->id }}" {{ old('treatment_id') == $treatment->id ? 'selected' : '' }}>{{ $treatment->name }}</option>
                    @endforeach
                    <option value="other" {{ old('treatment_id') == 'other' ? 'selected' : '' }}>Overige</option>
                </select>
            </div>

            <div id="product_section" class="hidden">
                <label for="product_id" class="block text-sm font-semibold text-black mb-2">Selecteer een product</label>
                <select id="product_id" name="product_id" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">
                    <option value="">-- Selecteer een product --</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                    <option value="other" {{ old('product_id') == 'other' ? 'selected' : '' }}>Overige</option>
                </select>
            </div>

            <div>
                <label for="name" class="block text-sm font-semibold text-black mb-2">Naam</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-black mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-black mb-2">Telefoonnummer (optioneel)</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">
            </div>

            <div>
                <label for="subject" class="block text-sm font-semibold text-black mb-2">Onderwerp</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">
            </div>

            <div>
                <label for="message" class="block text-sm font-semibold text-black mb-2">Bericht</label>
                <textarea id="message" name="message" rows="6" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:border-olive">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-olive text-white font-semibold py-3 rounded hover:bg-opacity-90 transition">
                Verzenden
            </button>
        </form>
    </div>

    <script>
        const inquiryTypeSelect = document.getElementById('inquiry_type');
        const treatmentSection = document.getElementById('treatment_section');
        const productSection = document.getElementById('product_section');
        const treatmentSelect = document.getElementById('treatment_id');
        const productSelect = document.getElementById('product_id');

        function toggleSections() {
            const type = inquiryTypeSelect.value;
            treatmentSection.classList.add('hidden');
            productSection.classList.add('hidden');
            treatmentSelect.removeAttribute('required');
            productSelect.removeAttribute('required');

            if (type === 'treatment') {
                treatmentSection.classList.remove('hidden');
                treatmentSelect.setAttribute('required', 'required');
            } else if (type === 'product') {
                productSection.classList.remove('hidden');
                productSelect.setAttribute('required', 'required');
            }
        }

        inquiryTypeSelect.addEventListener('change', toggleSections);
        
        // Initialize on page load
        window.addEventListener('load', toggleSections);
    </script>
</x-base-layout>
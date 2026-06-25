<x-base-layout>

    {{-- Hero --}}
    <section class="relative text-white py-28 md:py-36 bg-cover bg-center" style="background-image: url('/images/hero_img.png')">
        <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/40 to-[#FBF8F1]"></div>
        <div class="relative container mx-auto px-4 text-center">
            <p class="text-sm uppercase tracking-[0.3em] text-white/80 mb-4 t-label">Kapsalon in het hart van Gelderland</p>
            <h1 class="text-5xl md:text-6xl font-bold mb-4 t-display text-white leading-tight">
                Je haar zit <span class="italic font-light">goed</span>
            </h1>
            <p class="text-lg md:text-xl text-white/90 max-w-xl mx-auto t-body">
                Persoonlijk advies, vakwerk en een kop koffie. Geen kapsalon-keten, gewoon goed werk om de hoek.
            </p>
            <div class="mt-8">
                <a href="{{ route('appointments') }}" class="inline-block px-8 py-3 bg-olive text-white rounded-lg hover:bg-opacity-90 font-semibold transition shadow-lg">
                    Bekijk beschikbare tijden
                </a>
            </div>
        </div>
    </section>

    {{-- Golf-overgang, signature element --}}
    <div class="relative -mt-1">
        <svg viewBox="0 0 1440 60" class="w-full h-auto block" preserveAspectRatio="none">
            <path fill="#FBF8F1" d="M0,32 C240,60 480,4 720,24 C960,44 1200,8 1440,30 L1440,60 L0,60 Z"></path>
        </svg>
    </div>

    {{-- Ons verhaal --}}
    <section class="py-20 bg-[#FBF8F1]">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-olive/10">
                    <p class="text-xs uppercase tracking-[0.2em] text-olive font-semibold mb-3 t-label">Waarom wij</p>
                    <h2 class="text-3xl font-bold text-[#2B2B28] mb-4 t-display">Tijd voor je haar, écht</h2>
                    <p class="text-gray-600 leading-relaxed t-body">
                        Bij ons sta je niet op de klok. We nemen de tijd om te luisteren naar wat je wil,
                        ook als je het zelf nog niet precies weet. Of je nou voor het eerst durft te knippen
                        of al jaren bij ons komt: je krijgt advies dat past bij jouw haar, jouw gezicht en
                        jouw leven, niet bij de trend van deze maand.
                    </p>
                </div>

                <div class="bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-olive/10">
                    <p class="text-xs uppercase tracking-[0.2em] text-olive font-semibold mb-3 t-label">Onze aanpak</p>
                    <h2 class="text-3xl font-bold text-[#2B2B28] mb-4 t-display">Vakwerk zonder poeha</h2>
                    <p class="text-gray-600 leading-relaxed t-body">
                        Onze kappers blijven zich ontwikkelen in kleurtechnieken, knipmethoden en de
                        nieuwste verzorgingsproducten. Niet om indruk te maken, maar omdat jij dat resultaat
                        verdient. Je boekt online, kiest zelf je kapper en weet vooraf wat je kwijt bent.
                        Geen verrassingen, alleen goed haar.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Specialisaties --}}
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs uppercase tracking-[0.2em] text-olive font-semibold mb-3 t-label">Specialisaties</p>
                <h2 class="text-4xl font-bold text-[#2B2B28] t-display">Waar we goed in zijn</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-[#FBF8F1] border-l-4 border-olive">
                    <h3 class="text-2xl font-bold text-olive mb-3 t-display">Kleuring</h3>
                    <p class="text-gray-600 t-body">
                        Van een subtiele uitgroeibehandeling tot een complete kleurverandering.
                        Inclusief toning en kleurcorrectie als het net niet goed zat bij een ander.
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-[#FBF8F1] border-l-4 border-[#C97B5F]">
                    <h3 class="text-2xl font-bold text-[#C97B5F] mb-3 t-display">Balayage &amp; highlights</h3>
                    <p class="text-gray-600 t-body">
                        Natuurlijke overgangen en zachte accenten die meegroeien. Geen harde lijnen,
                        wel een hoofd vol diepte en glans.
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-[#FBF8F1] border-l-4 border-olive">
                    <h3 class="text-2xl font-bold text-olive mb-3 t-display">Knippen</h3>
                    <p class="text-gray-600 t-body">
                        Dameknipbeurten, laagjes, restyling en bijpunten. Voor wie precies weet wat ze wil,
                        en voor wie daar samen met ons achter wil komen.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Golf-overgang --}}
    <div class="relative">
        <svg viewBox="0 0 1440 60" class="w-full h-auto block" preserveAspectRatio="none">
            <path fill="#FBF8F1" d="M0,30 C240,8 480,52 720,28 C960,4 1200,40 1440,20 L1440,60 L0,60 Z"></path>
        </svg>
    </div>

    {{-- Behandelingen, uit de database --}}
    <section class="py-20 bg-[#FBF8F1]">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs uppercase tracking-[0.2em] text-olive font-semibold mb-3 t-label">Het aanbod</p>
                <h2 class="text-4xl font-bold text-[#2B2B28] t-display">Behandelingen</h2>
                <p class="text-gray-600 mt-3 t-body">Een overzicht van alles wat we voor je kunnen doen.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($treatments as $treatment)
                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition border border-olive/10">
                        <h3 class="text-lg font-semibold text-[#2B2B28]">{{ $treatment->name }}</h3>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-400 py-8">
                        Er zijn nog geen behandelingen toegevoegd.
                    </p>
                @endforelse
            </div>

            <div class="text-center mt-14">
                <a href="{{ route('appointments') }}" class="inline-block px-8 py-3 bg-olive text-white rounded-lg hover:bg-opacity-90 font-semibold transition shadow-md">
                    Boek nu
                </a>
            </div>
        </div>
    </section>

</x-base-layout>
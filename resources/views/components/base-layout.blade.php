<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Je haar zit goed - Kapsalon</title>
    
    <link rel="stylesheet" href="{{ asset('resources/fonts/fonts.css') }}">    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: var(--font-body); }
        
        .bg-olive { background-color: #6B8E23; }
        .text-olive { color: #6B8E23; }
        .border-olive { border-color: #6B8E23; }
        .hover-olive:hover { background-color: #556b1a; }
    </style>
</head>
<body class="bg-white text-black">
    <!-- Header/Navigation -->
    <header class="bg-white text-black shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-0 flex justify-between items-center">
            <div class="h-16 md:h-20">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo_nobg.png') }}" alt="Je haar zit goed" class="h-full object-contain">
                </a>
            </div>

            {{-- Desktop menu --}}
            <ul class="hidden md:flex gap-6 lg:gap-8 items-center">
                <li><a href="{{ route('appointments') }}" class="text-gray-700 hover:text-olive t-label transition">Kalender</a></li>
                <li><a href="{{ route('products') }}" class="text-gray-700 hover:text-olive t-label transition">Producten</a></li>
                <li><a href="{{ route('contact.index') }}" class="text-gray-700 hover:text-olive t-label transition">Contact</a></li>
                @auth
                    <li>
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-olive t-label transition">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                                Uitloggen
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}" class="bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                            Inloggen
                        </a>
                    </li>
                @endauth
                <li>
                    <a href="{{ route('cart.index') }}" class="text-2xl text-gray-700 hover:text-olive transition" title="Mandje" aria-label="Mandje">
                        🛒
                    </a>
                </li>
            </ul>

            {{-- Mobile: mandje + hamburger --}}
            <div class="flex md:hidden items-center gap-4">
                <a href="{{ route('cart.index') }}" class="text-2xl text-gray-700 hover:text-olive transition" title="Mandje" aria-label="Mandje">
                    🛒
                </a>
                <button onclick="toggleMobileMenu()" id="hamburgerButton" class="text-gray-700 p-2" aria-label="Menu" aria-expanded="false">
                    <svg id="hamburgerIcon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="closeIcon" class="w-7 h-7 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </nav>

        {{-- Mobile dropdown menu --}}
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-lg">
            <ul class="flex flex-col px-4 py-2">
                <li><a href="{{ route('appointments') }}" class="block py-3 text-gray-700 hover:text-olive t-label transition border-b border-gray-100">Kalender</a></li>
                <li><a href="{{ route('products') }}" class="block py-3 text-gray-700 hover:text-olive t-label transition border-b border-gray-100">Producten</a></li>
                <li><a href="{{ route('contact.index') }}" class="block py-3 text-gray-700 hover:text-olive t-label transition border-b border-gray-100">Contact</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}" class="block py-3 text-gray-700 hover:text-olive t-label transition border-b border-gray-100">Dashboard</a></li>
                    <li><a href="{{ route('profile.edit') }}" class="block py-3 text-gray-700 hover:text-olive t-label transition border-b border-gray-100">{{ Auth::user()->name }}</a></li>
                    <li class="py-3">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                                Uitloggen
                            </button>
                        </form>
                    </li>
                @else
                    <li class="py-3">
                        <a href="{{ route('login') }}" class="block w-full text-center bg-olive text-white px-4 py-2 rounded hover:bg-opacity-80 transition text-sm font-semibold">
                            Inloggen
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Cookie Aanpassingen -->
    <div id="cookieModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full text-black">
            <h2 class="text-xl font-bold mb-4 text-olive">Cookie instellingen</h2>
            
            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" checked disabled class="w-4 h-4">
                    <span class="font-semibold">Essentieel (verplicht)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="analytics" class="w-4 h-4">
                    <span class="font-semibold">Analytics</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" id="marketing" class="w-4 h-4">
                    <span class="font-semibold">Marketing</span>
                </label>
            </div>
            
            <div class="flex gap-2">
                <button onclick="toggleModal()" class="flex-1 px-3 py-2 bg-gray-200 rounded text-black hover:bg-gray-300">
                    Annuleren
                </button>
                <button onclick="saveCookies()" class="flex-1 px-3 py-2 bg-olive rounded text-white hover:bg-opacity-80 font-semibold">
                    Opslaan
                </button>
            </div>
        </div>
    </div>

    <!-- Cookie banner -->
    <div id="cookieBanner" class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4 z-40 hidden">
        <div class="container mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm flex-1 text-center sm:text-left">🍪 We gebruiken cookies. Door onze site te gebruiken ga je akkoord met ons cookiebeleid.</p>
            <div class="flex gap-2 flex-wrap justify-center">
                <button onclick="closeBanner()" class="px-3 py-1 text-gray-300 hover:text-white text-sm">
                    ✕
                </button>
                <button onclick="toggleModal()" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600 text-sm">
                    Personaliseer
                </button>
                <button onclick="acceptAll()" class="px-3 py-1 bg-olive rounded hover:bg-opacity-80 font-semibold text-sm">
                    Accepteren
                </button>
            </div>
        </div>
    </div>

    <!-- Cookie knopje rechts onder -->
    <div id="cookieButton" class="fixed bottom-4 right-4 z-40 hidden">
        <button onclick="toggleCookieMenu()" class="bg-olive text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg hover:shadow-2xl hover:scale-110 transition-all duration-200" title="Cookie instellingen">
            🍪
        </button>
        <div id="cookieMenu" class="absolute bottom-full right-0 mb-2 bg-white text-black rounded-lg shadow-lg hidden" style="min-width: 150px;">
            <button onclick="toggleModal()" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 rounded-t-lg font-semibold">
                Instellingen
            </button>
            <button onclick="clearCookies()" class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 rounded-b-lg border-t">
                Wissen
            </button>
        </div>
    </div>

    <footer class="bg-black text-white py-8 bg-cover bg-center relative" style="background-image: url('{{ asset('images/footer_bg.png') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-60"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mb-8 text-center sm:text-left">
                <div>
                    <img src="{{ asset('images/logo_nobgw.png') }}" alt="Je haar zit goed" class="h-20 object-contain mb-4 mx-auto sm:mx-0">
                    <p class="text-sm">Een buurtkapsalon waar je niet op de klok staat. Persoonlijk advies, vakwerk en een kop koffie, telkens weer.</p>
                </div>
                <div>
                    <h3 class="text-olive font-bold mb-4">Openingstijden</h3>
                    <p class="text-sm">Maandag - Vrijdag: 09:00 - 18:00</p>
                    <p class="text-sm">Zaterdag: 09:00 - 17:00</p>
                    <p class="text-sm">Zondag: Gesloten</p>
                </div>
                <div>
                    <h3 class="text-olive font-bold mb-4">Contact</h3>
                    <p class="text-sm">📞 06 1234 5678</p>
                    <p class="text-sm">📧 info@jehaarzitgoed.nl</p>
                </div>
            </div>
            <div class="border-t border-olive pt-4 text-center text-sm">
                <p>&copy; 2026 Je haar zit goed. Alle rechten voorbehouden.</p>
                <p class="mt-2"><a href="{{ route('privacy-policy') }}" class="text-olive hover:underline">Privacyverklaring</a></p>
            </div>
        </div>
    </footer>

    <script>
        const modal = document.getElementById('cookieModal');
        const banner = document.getElementById('cookieBanner');
        const cookieBtn = document.getElementById('cookieButton');
        const cookieMenu = document.getElementById('cookieMenu');
        const mobileMenu = document.getElementById('mobileMenu');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const closeIcon = document.getElementById('closeIcon');
        const hamburgerButton = document.getElementById('hamburgerButton');

        document.addEventListener('DOMContentLoaded', () => {
            if (!localStorage.getItem('cookieConsent')) {
                banner.classList.remove('hidden');
            } else {
                cookieBtn.classList.remove('hidden');
            }
        });

        function toggleMobileMenu() {
            const isOpen = !mobileMenu.classList.contains('hidden');
            mobileMenu.classList.toggle('hidden');
            hamburgerIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
            hamburgerButton.setAttribute('aria-expanded', String(!isOpen));
        }

        function toggleModal() {
            const stored = localStorage.getItem('cookieConsent');
            if (stored) {
                const prefs = JSON.parse(stored);
                document.getElementById('analytics').checked = prefs.analytics;
                document.getElementById('marketing').checked = prefs.marketing;
            }
            modal.classList.toggle('hidden');
            if (cookieMenu) cookieMenu.classList.add('hidden');
        }

        function toggleCookieMenu() {
            cookieMenu.classList.toggle('hidden');
        }

        function acceptAll() {
            saveCookies(true, true);
        }

        function saveCookies(analytics = null, marketing = null) {
            const prefs = {
                essential: true,
                analytics: analytics !== null ? analytics : document.getElementById('analytics').checked,
                marketing: marketing !== null ? marketing : document.getElementById('marketing').checked
            };
            localStorage.setItem('cookieConsent', JSON.stringify(prefs));
            modal.classList.add('hidden');
            banner.classList.add('hidden');
            cookieBtn.classList.remove('hidden');
            if (cookieMenu) cookieMenu.classList.add('hidden');
        }

        function closeBanner() {
            banner.classList.add('hidden');
        }

        function clearCookies() {
            localStorage.removeItem('cookieConsent');
            cookieBtn.classList.add('hidden');
            banner.classList.remove('hidden');
            if (cookieMenu) cookieMenu.classList.add('hidden');
        }
    </script>
</body>
</html>
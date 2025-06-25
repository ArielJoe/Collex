<nav class="bg-white shadow-sm py-3 px-4 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a class="text-2xl font-bold text-red-600 tracking-tight" href="{{ url('/') }}">
            <img src="{{ asset('collex.png') }}" alt="Collex Logo" width="150px">
        </a>

        <button class="md:hidden text-gray-600 hover:text-red-600 focus:outline-none" id="mobileMenuButton"
            aria-label="Toggle mobile menu">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <div class="hidden md:flex items-center gap-4" id="navbarContent">
            <ul class="flex items-center">
                <li>
                    <a class="px-4 py-2 text-lg {{ request()->is('/') ? 'rounded-lg text-red-600 font-bold hover:bg-red-600 hover:text-white' : 'text-gray-600 hover:text-red-600' }} transition"
                        href="{{ url('/') }}">Events</a>
                </li>
                {{-- Cart Link for Members (Desktop) --}}
                @if (session('userId') && session('role') === 'member')
                    <li>
                        <a class="px-4 py-2 text-lg {{ request()->routeIs('cart.index') ? 'rounded-lg text-red-600 font-bold hover:bg-red-600 hover:text-white' : 'text-gray-600 hover:text-red-600' }} transition relative"
                            href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart mr-1"></i>
                            {{-- Keranjang --}}
                            {{-- Anda bisa menambahkan counter item keranjang di sini jika ada --}}
                            {{-- <span class="absolute top-0 right-0 -mt-1 -mr-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center cart-item-count">0</span> --}}
                        </a>
                    </li>
                @endif
            </ul>

            <div class="flex items-center gap-3">
                @if (session('userId'))
                    <div class="relative">
                        <button
                            class="cursor-pointer flex items-center border border-gray-500 text-gray-500 px-3 py-2 rounded hover:bg-gray-500 hover:text-white transition"
                            id="userDropdown" aria-expanded="false" aria-haspopup="true">
                            <i class="fas fa-user-circle mr-2"></i>
                            <p>{{ session('full_name') }}</p>
                        </button>
                        <ul class="absolute right-0 mt-2 w-60 bg-white rounded-lg shadow-lg hidden py-1"
                            id="userDropdownMenu">
                            {{-- <li><a class="block px-4 py-2 text-gray-700 hover:bg-gray-100" href="#"><i
                                        class="fas fa-user mr-2"></i>Profile</a></li> --}}
                            @if (session('role') === 'member')
                                <li>
                                    <a class="block px-4 py-3 text-gray-700 hover:bg-gray-100" href="/member">
                                        <i class="fas fa-ticket-alt mr-2"></i>My Tickets
                                    </a>
                                </li>
                                <li>
                                    <a class="block px-4 py-3 text-gray-700 hover:bg-gray-100"
                                        href="/member/certificates">
                                        <i class="fas fa-certificate mr-2"></i>Certificates
                                    </a>
                                </li>
                            @endif
                            @if (session('role') === 'admin')
                                <li>
                                    <a class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                                        href="{{ route('admin.dashboard') }}"> {{-- Asumsi ada route admin.dashboard --}}
                                        <i class="fa-solid fa-table-columns mr-2"></i>Admin Dashboard
                                    </a>
                                </li>
                            @endif
                            @if (session('role') === 'organizer')
                                <li>
                                    <a class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                                        href="{{ route('organizer.index') }}"> {{-- Asumsi ada route organizer.index --}}
                                        <i class="fa-solid fa-table-columns mr-2"></i>Organizer Dashboard
                                    </a>
                                </li>
                            @endif
                            @if (session('role') === 'finance')
                                <li>
                                    <a class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                                        href="{{ route('finance.index') }}">
                                        <i class="fas fa-money-check-alt mr-2"></i>Finance Dashboard
                                    </a>
                                </li>
                            @endif
                            <li>
                                <hr class="border-gray-200 my-1">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="cursor-pointer block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100"><i
                                            class="fas fa-sign-out-alt mr-2"></i>Log out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="border border-gray-500 text-gray-500 px-3 py-2 rounded hover:bg-gray-500 hover:text-white transition">
                        <i class="fas fa-sign-in-alt mr-1"></i> Log in
                    </a>
                    <a href="{{ route('register') }}"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                        <i class="fas fa-user-plus mr-1"></i> Sign Up
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="md:hidden absolute top-16 left-0 w-full bg-white shadow-lg hidden" id="mobileMenu">
        <ul class="flex flex-col items-start p-4">
            <li class="w-full">
                <a class="block px-4 py-3 text-lg {{ request()->is('/') ? 'text-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }} transition"
                    href="{{ url('/') }}">Events</a>
            </li>
            {{-- Cart Link for Members (Mobile) --}}
            @if (session('userId') && session('role') === 'member')
                <li class="w-full">
                    <a class="block px-4 py-3 text-lg {{ request()->routeIs('cart.index') ? 'text-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }} transition"
                        href="{{ route('cart.index') }}">
                        <i class="fas fa-shopping-cart mr-2"></i>Keranjang
                    </a>
                </li>
            @endif

            @if (!session('userId'))
                <li class="w-full mt-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('login') }}" class="block px-4 py-3 text-gray-600 hover:text-red-600 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Log in
                    </a>
                </li>
                <li class="w-full">
                    <a href="{{ route('register') }}"
                        class="block px-4 py-3 text-red-600 hover:text-red-700 transition">
                        <i class="fas fa-user-plus mr-2"></i> Sign Up
                    </a>
                </li>
            @else
                <li class="w-full mt-2 pt-2 border-t border-gray-100 relative">
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 text-gray-600 hover:text-red-600 transition"
                        id="mobileUserDropdownButton" aria-expanded="false" aria-haspopup="true">
                        <span><i class="fas fa-user-circle mr-2"></i> {{ session('full_name') }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <ul class="w-full bg-gray-50 rounded-b-lg shadow-inner hidden pl-4" id="mobileUserDropdownMenu">
                        {{-- <li><a class="block px-4 py-3 text-gray-700 hover:bg-gray-100" href="#"><i
                                    class="fas fa-user mr-2"></i>Profile</a></li> --}}
                        @if (session('role') === 'member')
                            <li>
                                <a class="block px-4 py-3 text-gray-700 hover:bg-gray-100" href="/member">
                                    <i class="fas fa-ticket-alt mr-2"></i>My Tickets
                                </a>
                            </li>
                            <li>
                                <a class="block px-4 py-3 text-gray-700 hover:bg-gray-100" href="/member/certificates">
                                    <i class="fas fa-certificate mr-2"></i>Certificates
                                </a>
                            </li>
                        @endif
                        @if (session('role') === 'admin')
                            <li>
                                <a class="block px-4 py-3 text-gray-700 hover:bg-gray-100"
                                    href="{{ route('admin.dashboard') }}">
                                    <i class="fa-solid fa-table-columns mr-2"></i>Admin Dashboard
                                </a>
                            </li>
                        @endif
                        @if (session('role') === 'organizer')
                            <li>
                                <a class="block px-4 py-3 text-gray-700 hover:bg-gray-100"
                                    href="{{ route('organizer.index') }}">
                                    <i class="fa-solid fa-table-columns mr-2"></i>Organizer Dashboard
                                </a>
                            </li>
                        @endif
                        @if (session('role') === 'finance')
                            <li>
                                <a class="block px-4 py-3 text-gray-700 hover:bg-gray-100"
                                    href="{{ route('finance.index') }}">
                                    <i class="fas fa-money-check-alt mr-2"></i>Finance Dashboard
                                </a>
                            </li>
                        @endif
                        <li>
                            <hr class="border-gray-200 my-1">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-3 text-red-600 hover:bg-gray-100"><i
                                        class="fas fa-sign-out-alt mr-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const userDropdownButton = document.getElementById('userDropdown');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        const mobileUserDropdownButton = document.getElementById('mobileUserDropdownButton');
        const mobileUserDropdownMenu = document.getElementById('mobileUserDropdownMenu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                // Jika menu mobile terbuka, pastikan dropdown user mobile tertutup
                if (!mobileMenu.classList.contains('hidden') && mobileUserDropdownMenu && !
                    mobileUserDropdownMenu.classList.contains('hidden')) {
                    mobileUserDropdownMenu.classList.add('hidden');
                }
            });
        }

        if (userDropdownButton && userDropdownMenu) {
            userDropdownButton.addEventListener('click', function(event) {
                event.stopPropagation(); // Mencegah event klik menyebar ke window
                userDropdownMenu.classList.toggle('hidden');
            });
            // Menutup dropdown jika diklik di luar area dropdown (desktop)
            window.addEventListener('click', function(event) {
                if (userDropdownMenu && !userDropdownMenu.classList.contains('hidden') && !
                    userDropdownButton.contains(event.target) && !userDropdownMenu.contains(event
                        .target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            });
        }

        if (mobileUserDropdownButton && mobileUserDropdownMenu) {
            mobileUserDropdownButton.addEventListener('click', function() {
                mobileUserDropdownMenu.classList.toggle('hidden');
                mobileUserDropdownButton.querySelector('.fa-chevron-down').classList.toggle(
                    'rotate-180');
            });
        }
    });
</script>

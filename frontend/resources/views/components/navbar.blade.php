<nav class="bg-white shadow-sm py-3 px-4 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Brand Name -->
        <a class="text-2xl font-bold text-red-600 tracking-tight" href="{{ url('/') }}">Tixin</a>

        <!-- Hamburger Menu for Mobile -->
        <button class="md:hidden text-gray-600 hover:text-red-600 focus:outline-none" id="mobileMenuButton"
            aria-label="Toggle mobile menu">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <!-- Navbar Links and Buttons (Desktop) -->
        <div class="hidden md:flex items-center gap-4" id="navbarContent">
            <ul class="flex items-center">
                <!-- Events Link -->
                <li>
                    <a class="px-4 py-2 text-lg {{ request()->is('/') ? 'rounded-lg text-red-600 font-bold hover:bg-red-600 hover:text-white' : 'text-gray-600 hover:text-red-600' }} transition"
                        href="{{ url('/') }}">Events</a>
                </li>
            </ul>

            <!-- Login/Register Buttons -->
            <div class="flex items-center gap-3">
                @if (session('userId'))
                    <div class="relative">
                        <button
                            class="cursor-pointer flex items-center border border-gray-500 text-gray-500 px-3 py-2 rounded hover:bg-gray-500 hover:text-white transition"
                            id="userDropdown" aria-expanded="false" aria-haspopup="true">
                            <i class="fas fa-user-circle mr-2"></i>
                            <p>{{ session('full_name') }}</p>
                        </button>
                        <ul class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg hidden"
                            id="userDropdownMenu">
                            <li><a class="block px-4 py-2 text-gray-700 hover:bg-gray-100" href="#"><i
                                        class="fas fa-user mr-2"></i>Profile</a></li>
                            @if (session('role') === 'member')
                                <li><a class="block px-4 py-2 text-gray-700 hover:bg-gray-100" href="/member"><i
                                            class="fas fa-ticket-alt mr-2"></i>My Tickets</a></li>
                            @endif
                            @if (session('role') === 'admin')
                                <li><a class="block px-4 py-2 text-gray-700 hover:bg-gray-100" href="/admin"><i
                                            class="fa-solid fa-table-columns mr-2"></i>Dashboard</a></li>
                            @endif
                            <li>
                                <hr class="border-gray-200">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="cursor-pointer block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100"><i
                                            class="fas fa-sign-out-alt mr-2"></i>Logout</button>
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

        <!-- Mobile Menu (Hidden by Default) -->
        <div class="md:hidden absolute top-14 left-0 w-full bg-white shadow-lg hidden" id="mobileMenu">
            <ul class="flex flex-col items-start p-4">
                <li class="w-full">
                    <a class="block px-4 py-2 text-lg {{ request()->is('/') ? 'text-red-600 font-bold' : 'text-gray-600 hover:text-red-600' }} transition"
                        href="{{ url('/') }}">Events</a>
                </li>
                @if (!session('userId'))
                    <li class="w-full">
                        <a href="{{ route('login') }}"
                            class="block px-4 py-2 text-gray-600 hover:text-red-600 transition">
                            <i class="fas fa-sign-in-alt mr-1"></i> Log in
                        </a>
                    </li>
                    <li class="w-full">
                        <a href="{{ route('register') }}"
                            class="block px-4 py-2 text-red-600 hover:text-red-700 transition">
                            <i class="fas fa-user-plus mr-1"></i> Sign Up
                        </a>
                    </li>
                @else
                    <li class="w-full relative">
                        <button class="flex items-center px-4 py-2 text-gray-600 hover:text-red-600 transition"
                            id="mobileUserDropdown" aria-expanded="false" aria-haspopup="true">
                            <i class="fas fa-user-circle mr-2"></i> {{ session('full_name') }}
                        </button>
                        <ul class="w-full bg-white rounded-lg shadow-lg border hidden" id="mobileUserDropdownMenu">
                            <li><a class="block px-4 py-2 text-gray-700 hover:bg-gray-100" href="#"><i
                                        class="fas fa-user mr-2"></i>Profile</a></li>
                            @if (session('role') === 'member')
                                <li><a class="block px-4 py-2 text-gray-700 hover:bg-gray-100" href="/member"><i
                                            class="fas fa-ticket-alt mr-2"></i>My Tickets</a></li>
                            @endif
                            @if (session('role') === 'admin')
                                <li><a class="block px-4 py-2 text-gray-700 hover:bg-gray-100" href="/admin"><i
                                            class="fa-solid fa-table-columns mr-2"></i>Dashboard</a></li>
                            @endif
                            <li>
                                <hr class="border-gray-200">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100"><i
                                            class="fas fa-sign-out-alt mr-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<script>
    // Toggle mobile menu
    document.getElementById('mobileMenuButton')?.addEventListener('click', function() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    });

    // Toggle desktop user dropdown
    document.getElementById('userDropdown')?.addEventListener('click', function() {
        const dropdown = document.getElementById('userDropdownMenu');
        dropdown.classList.toggle('hidden');
    });

    // Toggle mobile user dropdown
    document.getElementById('mobileUserDropdown')?.addEventListener('click', function() {
        const dropdown = document.getElementById('mobileUserDropdownMenu');
        dropdown.classList.toggle('hidden');
    });
</script>

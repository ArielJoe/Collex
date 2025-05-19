<nav class="bg-white shadow-lg sticky top-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-semibold text-gray-800">Tixin Admin</h1>
                {{-- @if (session('email'))
                    <span class="text-sm px-2 py-1 rounded bg-gray-100 text-gray-600 capitalize">
                        {{ session('role') }}
                    </span>
                @endif --}}
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Dashboard Link -->
                <a href="{{ route('admin.dashboard') }}"
                    class="text-sm px-3 py-2 rounded-md transition duration-200 
                    {{ request()->is('admin') ? 'bg-red-600/20 text-red-600 font-medium' : 'text-gray-600 hover:bg-red-600/20 hover:text-red-600' }}">
                    Dashboard
                </a>

                <!-- User Type Links -->
                @php
                    $currentRole = request()->query('role');
                @endphp

                <a href="{{ route('admin.user.index', ['role' => 'member']) }}"
                    class="text-sm px-3 py-2 rounded-md transition duration-200 
                    {{ $currentRole === 'member' ? 'bg-red-600/20 text-red-600 font-medium' : 'text-gray-600 hover:bg-red-600/20 hover:text-red-600' }}">
                    Members
                </a>

                <a href="{{ route('admin.user.index', ['role' => 'finance']) }}"
                    class="text-sm px-3 py-2 rounded-md transition duration-200 
                    {{ $currentRole === 'finance' ? 'bg-red-600/20 text-red-600 font-medium' : 'text-gray-600 hover:bg-red-600/20 hover:text-red-600' }}">
                    Finances
                </a>

                <a href="{{ route('admin.user.index', ['role' => 'organizer']) }}"
                    class="text-sm px-3 py-2 rounded-md transition duration-200 
                    {{ $currentRole === 'organizer' ? 'bg-red-600/20 text-red-600 font-medium' : 'text-gray-600 hover:bg-red-600/20 hover:text-red-600' }}">
                    Organizers
                </a>

                <!-- Home Link -->
                <a href="/"
                    class="text-sm px-3 py-2 rounded-md transition duration-200 text-gray-600 hover:bg-red-600/20 hover:text-red-600">
                    Home
                </a>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="cursor-pointer text-sm px-3 py-2 rounded-md transition duration-200 text-gray-600 hover:bg-red-600/20 hover:text-red-600">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

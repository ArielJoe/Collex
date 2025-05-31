<aside
    class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white border-r border-gray-200 shadow-sm flex flex-col"
    aria-label="Sidebar">
    <div class="h-full px-3 py-4 overflow-y-auto">
        <div class="mb-8 text-center">
            <a class="flex flex-col items-center justify-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('collex.png') }}" alt="Collex Logo" class="h-10 mb-2">
                <span class="text-xl font-semibold whitespace-nowrap text-gray-800">Admin Panel</span>
            </a>
        </div>

        <ul class="space-y-1 font-medium">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center p-2 rounded-lg transition duration-150 group
                          {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i
                        class="fas fa-tachometer-alt w-5 h-5 transition duration-150 {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.index') ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>

            <li>
                <button type="button"
                    class="flex items-center w-full p-2 text-base text-gray-600 rounded-lg transition duration-150 group hover:bg-gray-50 hover:text-gray-900"
                    aria-controls="dropdown-user-management" data-collapse-toggle="dropdown-user-management">
                    <i class="fas fa-users w-5 h-5 text-gray-400 group-hover:text-gray-600 transition duration-150"></i>
                    <span class="flex-1 ml-3 text-left whitespace-nowrap">User Management</span>
                    <i class="fas fa-chevron-down w-3 h-3 transition-transform duration-200"></i>
                </button>
                <ul id="dropdown-user-management" class="hidden py-1 space-y-1 pl-4 bg-gray-50 rounded-lg">
                    @php
                        $currentRole = request()->query('role');
                    @endphp
                    <li>
                        <a href="{{ route('admin.user.index') }}"
                            class="flex items-center w-full p-2 text-gray-600 transition duration-150 rounded-lg pl-5 group hover:bg-gray-100 hover:text-gray-900
                                  {{ request()->routeIs('admin.user.index') && !$currentRole ? 'bg-gray-100 text-gray-900' : '' }}">
                            <i
                                class="fas fa-list-ul w-4 h-4 mr-2 {{ request()->routeIs('admin.user.index') && !$currentRole ? 'text-gray-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            All Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.user.index', ['role' => 'member']) }}"
                            class="flex items-center w-full p-2 text-gray-600 transition duration-150 rounded-lg pl-5 group hover:bg-gray-100 hover:text-gray-900
                                  {{ $currentRole === 'member' ? 'bg-gray-100 text-gray-900' : '' }}">
                            <i
                                class="fas fa-user-friends w-4 h-4 mr-2 {{ $currentRole === 'member' ? 'text-gray-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            Members
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.user.index', ['role' => 'finance']) }}"
                            class="flex items-center w-full p-2 text-gray-600 transition duration-150 rounded-lg pl-5 group hover:bg-gray-100 hover:text-gray-900
                                  {{ $currentRole === 'finance' ? 'bg-gray-100 text-gray-900' : '' }}">
                            <i
                                class="fas fa-file-invoice-dollar w-4 h-4 mr-2 {{ $currentRole === 'finance' ? 'text-gray-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            Finances
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.user.index', ['role' => 'organizer']) }}"
                            class="flex items-center w-full p-2 text-gray-600 transition duration-150 rounded-lg pl-5 group hover:bg-gray-100 hover:text-gray-900
                                  {{ $currentRole === 'organizer' ? 'bg-gray-100 text-gray-900' : '' }}">
                            <i
                                class="fas fa-user-cog w-4 h-4 mr-2 {{ $currentRole === 'organizer' ? 'text-gray-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            Organizers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.user.create') }}"
                            class="flex items-center w-full p-2 text-gray-600 transition duration-150 rounded-lg pl-5 group hover:bg-gray-100 hover:text-gray-900
                                  {{ request()->routeIs('admin.user.create') ? 'bg-gray-100 text-gray-900' : '' }}">
                            <i
                                class="fas fa-user-plus w-4 h-4 mr-2 {{ request()->routeIs('admin.user.create') ? 'text-gray-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                            Add New User
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul
            class="pt-4 mt-4 space-y-1 font-medium border-t border-gray-200 fixed bottom-0 w-[calc(100%-1.5rem)] pb-4 bg-white left-3 right-3">
            <li>
                <a href="{{ url('/') }}" target="_blank"
                    class="flex items-center p-2 text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900 group">
                    <i class="fas fa-home w-5 h-5 text-gray-400 group-hover:text-gray-600 transition duration-150"></i>
                    <span class="ml-3">View Main Site</span>
                </a>
            </li>
            <li>
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit"
                        class="flex items-center p-2 text-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 group w-full">
                        <i
                            class="fas fa-sign-out-alt w-5 h-5 text-red-500 group-hover:text-red-600 transition duration-150"></i>
                        <span class="ml-3">Log Out</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>

<!-- Main content area with left margin -->
<div class="sm:ml-64">
    <!-- Your main content goes here -->
</div>

<script>
    // Improved dropdown toggle script
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('[data-collapse-toggle]');

        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('aria-controls');
                const targetElement = document.getElementById(targetId);
                const chevron = this.querySelector('.fa-chevron-down, .fa-chevron-up');

                if (targetElement) {
                    targetElement.classList.toggle('hidden');

                    // Toggle chevron icon and rotation
                    if (chevron) {
                        chevron.classList.toggle('fa-chevron-down');
                        chevron.classList.toggle('fa-chevron-up');
                        chevron.classList.toggle('transform');
                        chevron.classList.toggle('rotate-180');
                    }

                    // Toggle active state styling
                    if (targetElement.classList.contains('hidden')) {
                        this.classList.remove('bg-gray-50', 'text-gray-900');
                    } else {
                        this.classList.add('bg-gray-50', 'text-gray-900');
                    }
                }
            });
        });
    });
</script>

@extends('components.layout')

@section('title')
    User Management
@endsection

@section('content')
    @include('components.admin.navbar')
    <div class="container mx-auto px-6 py-6 min-h-screen">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">User Management</h1>
                <p class="text-gray-600 mt-1">Manage all system users and their permissions</p>
            </div>
            <a href="{{ route('admin.user.create') }}"
                class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add New User
            </a>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Filters Section -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <form method="GET" class="w-full md:w-auto">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative">
                                <select name="role"
                                    class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-2 pl-4 pr-8 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all duration-200 cursor-pointer">
                                    <option value="">All Roles</option>
                                    <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member
                                    </option>
                                    <option value="finance" {{ request('role') == 'finance' ? 'selected' : '' }}>Finance
                                    </option>
                                    <option value="organizer" {{ request('role') == 'organizer' ? 'selected' : '' }}>
                                        Organizer</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm">
                                Filter
                            </button>
                        </div>
                    </form>

                    <div class="text-sm text-gray-500">
                        Showing {{ count($users) }} users
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-medium">
                                                {{ substr($user['full_name'], 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user['full_name'] }}</div>
                                            <div class="text-sm text-gray-500">Joined
                                                {{ \Carbon\Carbon::parse($user['created_at'])->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user['email'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user['phone_number'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleColors = [
                                            'member' => 'bg-purple-100 text-purple-800',
                                            'finance' => 'bg-green-100 text-green-800',
                                            'organizer' => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColors[$user['role']] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($user['role']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.user.show', $user['_id']) }}"
                                            class="text-blue-600 hover:text-blue-900 px-3 py-1.5 rounded-md hover:bg-blue-50 transition-colors duration-200 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>
                                        <a href="{{ route('admin.user.edit', $user['_id']) }}"
                                            class="text-indigo-600 hover:text-indigo-900 px-3 py-1.5 rounded-md hover:bg-indigo-50 transition-colors duration-200 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="mt-4 text-lg font-medium text-gray-700">No users found</h3>
                                        <p class="mt-1 text-gray-500">Try adjusting your search or filter to find what
                                            you're looking for.</p>
                                        <a href="{{ route('admin.user.create') }}"
                                            class="mt-4 text-blue-600 hover:text-blue-800 font-medium">
                                            Add your first user
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($totalPages > 1)
                <div class="px-6 py-4 border-t border-gray-100">
                    <nav class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing page {{ $currentPage }} of {{ $totalPages }}
                        </div>
                        <div class="flex space-x-2">
                            @if ($currentPage > 1)
                                <a href="?page={{ $currentPage - 1 }}&role={{ request('role') }}"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                </a>
                            @endif

                            <div class="flex space-x-1">
                                @php
                                    $start = max(1, $currentPage - 2);
                                    $end = min($totalPages, $currentPage + 2);
                                @endphp

                                @if ($start > 1)
                                    <a href="?page=1&role={{ request('role') }}"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        1
                                    </a>
                                    @if ($start > 2)
                                        <span class="px-3 py-1.5">...</span>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    <a href="?page={{ $i }}&role={{ request('role') }}"
                                        class="px-3 py-1.5 rounded-md border {{ $currentPage == $i ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }} transition-colors duration-200">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @if ($end < $totalPages)
                                    @if ($end < $totalPages - 1)
                                        <span class="px-3 py-1.5">...</span>
                                    @endif
                                    <a href="?page={{ $totalPages }}&role={{ request('role') }}"
                                        class="px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        {{ $totalPages }}
                                    </a>
                                @endif
                            </div>

                            @if ($currentPage < $totalPages)
                                <a href="?page={{ $currentPage + 1 }}&role={{ request('role') }}"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center">
                                    Next
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </nav>
                </div>
            @endif
        </div>
    </div>
@endsection

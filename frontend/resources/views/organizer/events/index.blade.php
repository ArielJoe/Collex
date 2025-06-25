@extends('components.layout')

@section('title')
    My Events
@endsection

@section('content')
    @include('components.navbar')
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Events</h1>
                <p class="text-gray-500 mt-1">Manage your upcoming and past events</p>
            </div>
            <a href="{{ route('organizer.events.create') }}"
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                Create New Event
            </a>
        </div>

        <!-- Alerts Section -->
        <div class="space-y-4 mb-8">
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-green-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                        <button type="button"
                            class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg p-1.5 hover:bg-green-100">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                )
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-red-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were {{ count($errors) }} errors with your
                                submission</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button"
                            class="ml-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg p-1.5 hover:bg-red-100">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Events Table -->
        @if (!empty($events) && count($events) > 0)
            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($events as $event)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden">
                                                @if (!empty($event['poster_url']))
                                                    <img class="h-full w-full object-cover"
                                                        src="{{ str_contains($event['poster_url'] ?? '', 'https') ? $event['poster_url'] : url($event['poster_url'] ?? '') }}"
                                                        alt="Event poster">
                                                @else
                                                    <div
                                                        class="h-full w-full bg-gray-100 flex items-center justify-center text-gray-400">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $event['name'] ?? 'Untitled Event' }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ isset($event['start_time']) ? \Carbon\Carbon::parse($event['start_time'])->setTimezone('Asia/Jakarta')->format('M d, Y') : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ isset($event['start_time']) ? \Carbon\Carbon::parse($event['start_time'])->setTimezone('Asia/Jakarta')->format('h:i A') : 'N/A' }}
                                            @if (isset($event['end_time']))
                                                <span class="text-gray-500"> -
                                                    {{ \Carbon\Carbon::parse($event['end_time'])->setTimezone('Asia/Jakarta')->format('h:i A') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $event['location'] ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $status = 'Upcoming';
                                            $statusColor = 'bg-blue-100 text-blue-800';
                                            if (
                                                isset($event['end_time']) &&
                                                \Carbon\Carbon::parse($event['end_time'])->isPast()
                                            ) {
                                                $status = 'Completed';
                                                $statusColor = 'bg-gray-100 text-gray-800';
                                            } elseif (
                                                isset($event['start_time']) &&
                                                \Carbon\Carbon::now()->between(
                                                    \Carbon\Carbon::parse($event['start_time']),
                                                    \Carbon\Carbon::parse($event['end_time'] ?? $event['start_time']),
                                                )
                                            ) {
                                                $status = 'Ongoing';
                                                $statusColor = 'bg-green-100 text-green-800';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('organizer.events.show', $event['_id'] ?? $event['id']) }}"
                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('organizer.events.edit', $event['_id'] ?? $event['id']) }}"
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                                                title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form
                                                action="{{ route('organizer.events.destroy', $event['_id'] ?? $event['id']) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this event?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($totalPages > 1)
                <div class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing page {{ $currentPage }} of {{ $totalPages }}
                    </div>
                    <nav class="flex space-x-2">
                        @if ($currentPage > 1)
                            <a href="?page={{ $currentPage - 1 }}"
                                class="px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                        @endif

                        @for ($i = 1; $i <= $totalPages; $i++)
                            <a href="?page={{ $i }}"
                                class="px-3 py-1.5 border rounded-md text-sm font-medium {{ $i == $currentPage ? 'border-blue-500 bg-blue-50 text-blue-600' : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}"
                                class="px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        @endif
                    </nav>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-xl shadow-sm">
                <div class="mx-auto w-24 h-24 mb-6 text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Events Found</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">You haven't created any events yet. Get started by creating
                    your first event.</p>
                <a href="{{ route('organizer.events.create') }}"
                    class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-medium rounded-lg shadow-sm transition-all duration-200 hover:shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Create Your First Event
                </a>
            </div>
        @endif
    </div>
    @include('components.footer')
@endsection

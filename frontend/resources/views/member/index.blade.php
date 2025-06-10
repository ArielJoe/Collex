@extends('components.layout')

@section('title')
    My Tickets
@endsection

@section('content')
    @include('components.navbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-10">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">My Tickets</h1>
            <p class="mt-2 text-sm text-gray-600">View and manage your event tickets here.</p>
        </header>

        @if ($error)
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-md" role="alert"
                aria-live="polite">
                <p class="font-semibold">Error</p>
                <p>{{ $error }}</p>
            </div>
        @endif

        <div class="space-y-6">
            @forelse ($tickets as $event)
                <div
                    class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl md:flex">
                    <div class="md:w-1/3 lg:w-1/4">
                        <img src="{{ $event['poster_url'] ?? 'https://via.placeholder.com/400x500.png?text=No+Image' }}"
                            alt="{{ $event['eventName'] }} Poster"
                            class="w-full h-48 md:h-full object-cover transition-opacity duration-300 hover:opacity-90">
                    </div>

                    <div class="p-6 md:w-2/3 lg:w-3/4 flex flex-col justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $event['eventName'] }}</h2>

                            <div class="border-t border-gray-200 my-4"></div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <span class="block text-sm font-medium text-gray-500">Payment Status</span>
                                    <p class="text-lg font-semibold text-gray-900">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            @class([
                                                'bg-green-100 text-green-800' => $event['paymentStatus'] === 'confirmed',
                                                'bg-yellow-100 text-yellow-800' => $event['paymentStatus'] === 'pending',
                                                'bg-gray-100 text-gray-800' =>
                                                    $event['paymentStatus'] !== 'confirmed' &&
                                                    $event['paymentStatus'] !== 'pending',
                                            ])>
                                            {{ $event['paymentStatus'] }}
                                        </span>
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <span class="block text-sm font-medium text-gray-500">Location</span>
                                    <p class="text-lg text-gray-700">{{ $event['location'] }}</p>
                                </div>
                                <div class="space-y-2">
                                    <span class="block text-sm font-medium text-gray-500">Event Starts</span>
                                    <p class="text-lg text-gray-700">
                                        {{ \Carbon\Carbon::parse($event['start_time'])->format('D, M j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <span class="block text-sm font-medium text-gray-500">Payment Confirmed</span>
                                    <p class="text-lg text-gray-700">
                                        @if ($event['confirmedAt'])
                                            {{ \Carbon\Carbon::parse($event['confirmedAt'])->format('D, M j, Y') }}
                                        @else
                                            <span class="text-yellow-600">Not confirmed yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <details class="group" aria-expanded="false">
                                    <summary
                                        class="flex justify-between items-center font-semibold text-gray-700 cursor-pointer py-2 bg-gray-50 rounded-lg px-4 transition-all duration-300 hover:bg-gray-100">
                                        <span>View Purchased Items</span>
                                        <span class="transition-transform duration-300 group-open:rotate-180">
                                            ▼
                                        </span>
                                    </summary>
                                    <div class="mt-2 space-y-2 p-4 bg-gray-50 rounded-lg">
                                        @forelse ($event['purchasedItems'] as $item)
                                            <div
                                                class="flex justify-between items-center p-3 bg-white rounded-md shadow-sm hover:bg-gray-100 transition-colors duration-200">
                                                <div>
                                                    <p class="text-md font-semibold text-gray-800">{{ $item['name'] }}
                                                        ({{ $item['type'] }})
                                                    </p>
                                                </div>
                                                <p class="text-md text-gray-600">
                                                    Rp{{ number_format(is_numeric($item['price']) ? $item['price'] : 0, 2) }}
                                                </p>
                                            </div>
                                        @empty
                                            <p class="text-md text-gray-600 text-center">No items purchased for this event.
                                            </p>
                                        @endforelse
                                    </div>
                                </details>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('event.show', ['id' => $event['eventId']]) }}"
                                class="inline-flex items-center bg-primary hover:bg-primary-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                View Ticket Details
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                @if (!$error)
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-md text-center"
                        role="alert" aria-live="polite">
                        <p>You do not have any confirmed tickets yet.</p>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
@endsection

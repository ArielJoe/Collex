@extends('components.layout')

@section('title')
    Finance Dashboard
@endsection

@section('content')
    @include('components.navbar')

    @if (session('role') !== 'finance')
        <div class="container mx-auto px-4 py-8 text-center">
            <p class="text-red-600">You do not have permission to access this page.</p>
            <a href="{{ url('/') }}" class="text-blue-600 hover:underline mt-4 inline-block">Back to Home</a>
        </div>
    @else
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold text-blue-900 mb-6">Payment Approvals</h1>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if (empty($payments))
                <p class="text-gray-600">No pending payments found.</p>
            @else
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 font-medium">User</th>
                                <th class="p-4 font-medium">Event</th>
                                <th class="p-4 font-medium">Amount</th>
                                <th class="p-4 font-medium">Status</th>
                                <th class="p-4 font-medium">Payment Proof</th>
                                <th class="p-4 font-medium">Date</th>
                                <th class="p-4 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4">
                                        @if (isset($payment['user_id']) && is_array($payment['user_id']))
                                            @php
                                                $user = is_array($payment['user_id'][0] ?? null)
                                                    ? $payment['user_id'][0]
                                                    : $payment['user_id'];
                                            @endphp
                                            <div class="font-medium">
                                                {{ $user['full_name'] ?? 'Unknown User' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $user['email'] ?? '' }}
                                            </div>
                                        @else
                                            User ID: {{ $payment['user_id'] ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if (isset($payment['all_registrations'][0]['event_id']['name']))
                                            <div class="font-medium">
                                                {{ $payment['all_registrations'][0]['event_id']['name'] }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $payment['all_registrations'][0]['detail_id']['title'] ?? 'General Admission' }}
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="p-4 font-medium">
                                        {{ isset($payment['amount']['$numberDecimal']) ? $payment['amount']['$numberDecimal'] : '0' }}
                                        IDR
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="px-2 py-1 text-xs rounded-full 
                                            {{ $payment['status'] === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : ($payment['status'] === 'confirmed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($payment['status'] ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if (isset($payment['proof_url']))
                                            <a href="{{ $payment['proof_url'] }}" target="_blank"
                                                class="text-blue-600 hover:underline flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                View
                                            </a>
                                        @else
                                            <span class="text-gray-400">Not provided</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-sm text-gray-500">
                                        {{ isset($payment['created_at']) ? \Carbon\Carbon::parse($payment['created_at'])->format('M d, Y H:i') : 'N/A' }}
                                    </td>
                                    <td class="p-4">
                                        @if (($payment['status'] ?? '') === 'pending')
                                            <div class="flex space-x-2">
                                                <form
                                                    action="{{ route('finance.approve-payment', $payment['_id'] ?? '') }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('finance.reject-payment', $payment['_id'] ?? '') }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-gray-500 text-sm">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (isset($totalPages) && $totalPages > 1)
                    <div class="mt-6 flex justify-center">
                        <nav class="flex items-center space-x-1">
                            @for ($i = 1; $i <= $totalPages; $i++)
                                <a href="{{ route('finance.index') }}?page={{ $i }}"
                                    class="px-3 py-1 rounded-md text-sm font-medium 
                                    {{ $i == $currentPage ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                                    {{ $i }}
                                </a>
                            @endfor
                        </nav>
                    </div>
                @endif
            @endif
        </div>
    @endif
@endsection

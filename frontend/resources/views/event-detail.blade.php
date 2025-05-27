@extends('components.layout')

@section('title')
    {{ $event['name'] }} | Event Details
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .event-details-card {
            transition: all 0.3s ease;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .event-image-container {
            height: 300px;
            width: 100%;
            overflow: hidden;
            position: relative;
        }

        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: opacity 0.3s ease;
        }

        .event-image-container:hover .event-image {
            opacity: 0.9;
        }

        .file-upload-container {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
            transition: all 0.2s ease;
        }

        .file-upload-container:hover {
            border-color: #9ca3af;
        }

        .file-upload-container.has-file {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .file-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 1rem auto 0;
            display: none;
            border-radius: 0.25rem;
            border: 1px solid #e5e7eb;
        }

        .loading-spinner {
            display: none;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .speaker-badge {
            background-color: #f3f4f6;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }

        .speaker-badge:hover {
            background-color: #e5e7eb;
        }

        .detail-icon {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }

        .registration-section {
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .submit-btn {
            transition: all 0.2s ease;
            position: relative;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .proof-submitted {
            background-color: #d1fae5;
            color: #10b981;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            display: inline-block;
        }

        .payment-status-card {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }
    </style>
@endpush

@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const API_BASE_URL = 'http://localhost:5000';
            const userId = '{{ session('userId') }}';
            const eventId = '{{ $event['_id'] }}';

            // Function to check payment status
            function checkPaymentStatus() {
                if (!userId || !eventId) {
                    console.log('Missing userId or eventId');
                    return;
                }

                console.log('Checking payment status for user:', userId, 'event:', eventId);

                $.ajax({
                    url: `${API_BASE_URL}/api/check-payment-status`,
                    method: 'GET',
                    data: {
                        user_id: userId,
                        event_id: eventId
                    },
                    success: function(response) {
                        console.log('Payment status response:', response);

                        if (response.proof_submitted || response.payment_status === 'completed' ||
                            response.registered) {
                            let submittedAt;
                            // Format the submitted date
                            if (response.submitted_at) {
                                try {
                                    submittedAt = new Date(response.submitted_at).toLocaleString(
                                        'en-US', {
                                            weekday: 'long',
                                            month: 'long',
                                            day: 'numeric',
                                            year: 'numeric',
                                            hour: 'numeric',
                                            minute: 'numeric',
                                            hour12: true
                                        }).replace(/PM|AM/, 'WIB');
                                } catch (e) {
                                    console.error('Date parsing error:', e);
                                }
                            }

                            // Update the payment status in the event details grid
                            $('#proofSubmittedDetail').html(`
                                <div class="flex items-start">
                                    <div class="detail-icon mr-3">
                                        <i class="fas fa-receipt text-green-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Payment Status</h3>
                                        <p class="mt-1 font-medium text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Payment Confirmed
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Submitted on ${submittedAt}
                                        </p>
                                    </div>
                                </div>
                            `);

                            // Show the success card
                            $('#paymentStatusCard').html(`
                                <div class="payment-status-card">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-2xl mr-3"></i>
                                        <div>
                                            <h3 class="text-lg font-semibold">Registration Confirmed!</h3>
                                            <p class="text-sm opacity-90">Your payment has been verified and your spot is secured.</p>
                                            <p class="text-xs opacity-75 mt-1">Confirmed on ${submittedAt}</p>
                                        </div>
                                    </div>
                                </div>
                            `).show();

                            // Hide the registration form
                            $('#registrationForm').hide();

                            console.log('Payment confirmed - UI updated');
                        } else {
                            // Show registration form if not paid
                            $('#registrationForm').show();
                            $('#paymentStatusCard').hide();
                            console.log('Payment not confirmed - showing registration form');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking payment status:', {
                            status: status,
                            error: error,
                            response: xhr.responseJSON
                        });

                        // Show registration form on error
                        $('#registrationForm').show();
                        $('#paymentStatusCard').hide();
                    }
                });
            }

            // File upload preview
            $('#proof_url').on('change', function() {
                const file = this.files[0];
                const container = $(this).closest('.file-upload-container');

                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert('error', 'File size exceeds 5MB limit');
                        $(this).val('');
                        return;
                    }

                    container.addClass('has-file');
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#filePreview').attr('src', e.target.result).show();
                        $('#fileName').text(file.name).show();
                    };

                    reader.readAsDataURL(file);
                } else {
                    container.removeClass('has-file');
                    $('#filePreview').hide();
                    $('#fileName').hide();
                }
            });

            // Handle form submission
            $(document).on('submit', '#registrationForm form', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = new FormData(form[0]);
                const submitBtn = form.find('button[type="submit"]');

                submitBtn.prop('disabled', true);
                submitBtn.prepend('<span class="loading-spinner mr-2 inline-block"></span>');
                $('.loading-spinner').show();

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showAlert('success', response.message || 'Registration successful!');
                        // Wait a moment then check payment status
                        setTimeout(() => {
                            checkPaymentStatus();
                        }, 1000);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON ?
                            (xhr.responseJSON.message || 'An error occurred') :
                            'An error occurred while processing your request';
                        showAlert('error', error);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        $('.loading-spinner').remove();
                    }
                });
            });

            function showAlert(type, message) {
                const alertHtml = `
                    <div class="p-4 mb-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} flex items-start">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mt-1 mr-2"></i>
                        <div>${message}</div>
                    </div>
                `;

                $('#alertsContainer').html(alertHtml);

                setTimeout(() => {
                    $('#alertsContainer').fadeOut(300, function() {
                        $(this).empty().show();
                    });
                }, 5000);
            }

            // Check payment status on page load if user is logged in
            @if (session('userId'))
                console.log('User logged in, checking payment status...');
                checkPaymentStatus();
            @else
                console.log('User not logged in');
            @endif
        });
    </script>
@endpush

@section('content')
    @include('components.navbar')
    <div class="container mx-auto p-4 md:p-6 max-w-6xl">
        <!-- Alerts Container -->
        <div id="alertsContainer" class="mb-4"></div>

        <!-- Event Image at Top -->
        <div class="event-image-container rounded-lg mb-6">
            <img src="{{ $event['poster_url'] }}" alt="{{ $event['name'] }}" class="event-image">
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden event-details-card p-6">
            <div class="flex flex-col">
                <!-- Event Header -->
                <div class="flex justify-between items-start mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $event['name'] }}</h1>
                    @if ($event['max_participants'])
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                            {{ $event['registered_participants'] ?? 0 }}/{{ $event['max_participants'] }} spots
                        </span>
                    @endif
                </div>

                <!-- Event Description -->
                <div class="prose max-w-none text-gray-600 mb-8">
                    {!! nl2br(e($event['description'])) !!}
                </div>

                <!-- Event Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="flex items-start">
                        <div class="detail-icon mr-3">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Date & Time</h3>
                            <p class="mt-1 font-medium">
                                {{ \Carbon\Carbon::parse($event['start_time'])->format('l, F j, Y') }}<br>
                                {{ \Carbon\Carbon::parse($event['start_time'])->format('g:i A') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="detail-icon mr-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Location</h3>
                            <p class="mt-1 font-medium">{{ $event['location'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="detail-icon mr-3">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Registration Fee</h3>
                            <p class="mt-1 font-medium">
                                ${{ number_format($event['registration_fee']['$numberDecimal'], 2) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="detail-icon mr-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Event Type</h3>
                            <p class="mt-1 font-medium">
                                {{ $event['max_participants'] > 0 ? 'Limited Seats' : 'Open Attendance' }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment Status Display (will be populated by JavaScript) -->
                    <div id="proofSubmittedDetail"></div>
                </div>

                @if ($event['speaker'])
                    <div class="mb-8">
                        <h3 class="text-sm font-medium text-gray-500 mb-3">Featured Speaker(s)</h3>
                        <div class="flex flex-wrap">
                            <span class="speaker-badge">
                                <i class="fas fa-user-tie text-gray-500 mr-2"></i>
                                {{ $event['speaker'] }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Registration Section -->
                <div class="registration-section">
                    @if (session('userId'))
                        <!-- Payment Status Card (shown when payment is confirmed) -->
                        <div id="paymentStatusCard" style="display: none;"></div>

                        <!-- Registration Form (shown when payment is not confirmed) -->
                        <div id="registrationForm" style="display: none;">
                            <form action="{{ route('event.register.and.pay', $event['_id']) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="amount"
                                    value="{{ $event['registration_fee']['$numberDecimal'] }}">

                                <h3 class="text-lg font-semibold mb-3 text-gray-800">Complete Your Registration</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Please upload proof of payment to complete your registration.
                                </p>

                                <div class="file-upload-container mb-4">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-500 mb-1">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-400">PNG, JPG, or PDF (Max. 5MB)</p>
                                        <input type="file" name="proof_url" id="proof_url"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required>
                                    </div>
                                    <img id="filePreview" class="file-preview" src="#" alt="Preview">
                                    <p id="fileName" class="text-sm font-medium text-gray-700 mt-2" style="display: none;">
                                    </p>
                                </div>

                                <button type="submit"
                                    class="submit-btn bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-full transition duration-300 flex items-center justify-center">
                                    Submit
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-blue-50 text-blue-800 p-4 rounded-lg flex items-start">
                            <i class="fas fa-info-circle mt-1 mr-2"></i>
                            <div>
                                Please <a href="{{ route('login') }}" class="font-semibold hover:underline">log in</a> to
                                register for this event.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

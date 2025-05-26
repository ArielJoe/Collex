@extends('components.layout')

@section('title')
    {{ $event['name'] }} | Event Details
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .payment-status {
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            display: inline-block;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #10b981;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #ef4444;
        }

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
            padding: 1.5rem;
            margin-top: 1.5rem;
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
    </style>
@endpush

@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const API_BASE_URL = '{{ env('API_BASE_URL', 'http://localhost:5000') }}';

            // Check payment status on page load
            @if (session('userId'))
                checkPaymentStatus();
            @endif

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
                    }

                    reader.readAsDataURL(file);
                } else {
                    container.removeClass('has-file');
                    $('#filePreview').hide();
                    $('#fileName').hide();
                }
            });

            // Handle form submission
            $('form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = new FormData(form[0]);
                const submitBtn = form.find('button[type="submit"]');

                // Show loading state
                submitBtn.prop('disabled', true);
                submitBtn.prepend('<span class="loading-spinner mr-2"></span>');
                $('.loading-spinner').show();

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showAlert('success', response.message || 'Registration successful!');
                        checkPaymentStatus();
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

            function checkPaymentStatus() {
                $('#paymentStatus').html(
                    '<div class="flex items-center text-gray-600"><i class="fas fa-spinner fa-spin mr-2"></i> Checking status...</div>'
                );

                $.ajax({
                    url: `${API_BASE_URL}/api/payment-status`,
                    method: 'GET',
                    data: {
                        user_id: '{{ session('userId') }}',
                        event_id: '{{ $event['_id'] }}'
                    },
                    success: function(response) {
                        if (response.payment) {
                            updatePaymentStatusUI(response.payment);
                        } else {
                            $('#paymentStatus').html(
                                '<span class="text-gray-600">No payment record found</span>');
                        }
                    },
                    error: function() {
                        $('#paymentStatus').html(
                            '<span class="text-red-500">Failed to check payment status</span>');
                    }
                });
            }

            function updatePaymentStatusUI(payment) {
                let statusClass, statusText, icon;

                switch (payment.payment_status) {
                    case 'completed':
                        statusClass = 'status-completed';
                        statusText = 'Payment Completed';
                        icon = 'fa-check-circle';
                        break;
                    case 'rejected':
                        statusClass = 'status-rejected';
                        statusText = 'Payment Rejected';
                        icon = 'fa-times-circle';
                        break;
                    default:
                        statusClass = 'status-pending';
                        statusText = 'Payment Pending';
                        icon = 'fa-clock';
                }

                const statusHtml = `
                    <div class="payment-status ${statusClass} flex items-center">
                        <i class="fas ${icon} mr-2"></i>
                        ${statusText}
                    </div>
                    ${payment.payment_status === 'pending' ? 
                        '<p class="text-sm text-gray-500 mt-2">Your payment is under review</p>' : ''}
                    ${payment.payment_status === 'rejected' ? 
                        '<p class="text-sm text-gray-500 mt-2">Please upload a new proof of payment</p>' : ''}
                `;

                $('#paymentStatus').html(statusHtml);

                // Hide the form if payment is completed
                if (payment.payment_status === 'completed') {
                    $('form').hide();
                    $('#registrationSuccess').show();
                } else if (payment.payment_status === 'rejected') {
                    $('form').show();
                }
            }

            function showAlert(type, message) {
                const alertHtml = `
                    <div class="p-4 mb-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'} flex items-start">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mt-1 mr-2"></i>
                        <div>${message}</div>
                    </div>
                `;

                $('#alertsContainer').html(alertHtml);

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    $('#alertsContainer').fadeOut(300, function() {
                        $(this).empty().show();
                    });
                }, 5000);
            }
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
                                {{ \Carbon\Carbon::parse($event['date_time'])->format('l, F j, Y') }}<br>
                                {{ \Carbon\Carbon::parse($event['date_time'])->format('g:i A') }}
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
                        <div id="paymentStatus" class="mb-4"></div>

                        <div id="registrationSuccess" class="hidden bg-green-50 text-green-800 p-4 rounded-lg mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>You're successfully registered for this event!</span>
                            </div>
                        </div>

                        @if (!isset($payment) || $payment['payment_status'] !== 'completed')
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
                                    Submit Payment Proof
                                </button>
                            </form>
                        @endif
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

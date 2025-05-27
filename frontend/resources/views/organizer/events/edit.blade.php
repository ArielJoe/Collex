@extends('components.layout')

@section('title', 'Edit Event - ' . ($event['name'] ?? 'Event'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white rounded-top">
                        <h2 class="mb-0">Edit Event: {{ $event['name'] ?? 'Untitled' }}</h2>
                    </div>

                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Whoops!</strong> There were some problems with your input.
                                <ul class="mb-0 mt-2 ps-4">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('organizer.events.update', $event['_id'] ?? $event['id']) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Event Name -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">Event Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="name" name="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name', $event['name'] ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date & Time -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label fw-semibold">Start Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" id="start_time" name="start_time"
                                        class="form-control @error('start_time') is-invalid @enderror"
                                        value="{{ old('start_time', isset($event['start_time']) ? \Carbon\Carbon::parse($event['start_time'])->format('Y-m-d\TH:i') : '') }}"
                                        required>
                                    @error('start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="end_time" class="form-label fw-semibold">End Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" id="end_time" name="end_time"
                                        class="form-control @error('end_time') is-invalid @enderror"
                                        value="{{ old('end_time', isset($event['end_time']) ? \Carbon\Carbon::parse($event['end_time'])->format('Y-m-d\TH:i') : '') }}"
                                        required>
                                    @error('end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="mb-4">
                                <label for="location" class="form-label fw-semibold">Location <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="location" name="location"
                                    class="form-control @error('location') is-invalid @enderror"
                                    value="{{ old('location', $event['location'] ?? '') }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Poster Upload -->
                            <div class="mb-4">
                                <label for="poster" class="form-label fw-semibold">Event Poster</label>

                                @if (!empty($event['poster_url']))
                                    <div class="mb-3">
                                        <img src="{{ asset($event['poster_url']) }}" alt="Current Poster"
                                            class="img-fluid rounded shadow-sm border" style="max-width: 200px;">
                                        <small class="form-text text-muted d-block mt-2">
                                            Current poster (will be replaced if you upload a new one)
                                        </small>
                                    </div>
                                @endif

                                <input type="file" id="poster" name="poster" accept="image/*"
                                    class="form-control @error('poster') is-invalid @enderror">
                                <small class="form-text text-muted mt-1 d-block">
                                    Accepted formats: JPG, PNG, GIF. Maximum size: 2MB. Leave empty to keep current poster.
                                </small>
                                @error('poster')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3">
                                <a href="{{ route('organizer.events.show', $event['_id'] ?? $event['id']) }}"
                                    class="btn btn-outline-secondary flex-grow">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Event
                                </a>
                                <button type="submit" class="btn btn-primary flex-grow">
                                    <i class="fas fa-save me-1"></i> Update Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

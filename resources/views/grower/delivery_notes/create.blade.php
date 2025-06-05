@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="fw-bold text-success fs-2 mb-4">Create Delivery Note</h1>

    <!-- Delivery Note Form -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light">
            <h5 class="mb-0">Add a New Delivery Note</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('grower.delivery-notes.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf

                <!-- Pre-filled Weekly Estimates -->
                <h5 class="mb-3">🌿 Pre-filled Weekly Estimates</h5>
                @forelse($weeklyPlans as $plan)
                    <div class="mb-3">
                        <label class="form-label">{{ $plan->commitment->cropOffering->name }}</label>
                        <input type="hidden" name="crops[{{ $loop->index }}][name]" value="{{ $plan->commitment->cropOffering->name }}">
                        <input type="number" step="0.1" class="form-control"
                               name="crops[{{ $loop->index }}][quantity]"
                               value="{{ $plan->estimate ?? '' }}"
                               placeholder="Quantity" required>
                        <small class="text-muted">Unit: {{ $plan->commitment->cropOffering->unit }}</small>
                    </div>
                @empty
                    <p class="text-muted">No crop estimates found for this week.</p>
                @endforelse

                <!-- Manual Crop Entry Section -->
                <h5 class="mb-3">➕ Manually Add Crops</h5>
                <div id="crop-entries"></div>
                <button type="button" class="btn btn-secondary mb-3" id="add-entry">Add Another Crop</button>

                <!-- Distributor Selection -->
                <div class="mb-3">
                    <label class="form-label">Select Distributor</label>
                    <select class="form-select" name="distributor_id" required>
                        <option value="">Select Distributor</option>
                        @foreach($distributors as $distributor)
                            <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Invoice Attachment -->
                <div class="mb-3">
                    <label class="form-label">Attach Invoice</label>
                    <input type="file" class="form-control" name="invoice">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-success">Submit Delivery Note</button>
            </form>

            <!-- Add Crop Entry Script -->
            <script>
                let cropIndex = {{ $weeklyPlans->count() }};
                document.getElementById('add-entry').addEventListener('click', function () {
                    const container = document.getElementById('crop-entries');
                    const entry = document.createElement('div');
                    entry.classList.add('mb-3');
                    entry.innerHTML = `
                        <label class="form-label">Crop</label>
                        <input type="text" class="form-control" name="crops[${cropIndex}][name]" required>
                        <label class="form-label mt-2">Quantity (kg/Units)</label>
                        <input type="number" class="form-control" name="crops[${cropIndex}][quantity]" required>
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-entry">Remove</button>
                    `;
                    container.appendChild(entry);
                    cropIndex++;
                });

                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-entry')) {
                        e.target.closest('div.mb-3').remove();
                    }
                });
            </script>
        </div>
    </div>

    <!-- Latest Delivery Note Display -->
    @isset($latest)
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">✅ Latest Delivery Note</h5>
        </div>
        <div class="card-body">
            <p><strong>Traceability #:</strong> {{ $latest->traceability_number }}</p>
            <p><strong>Destination:</strong> {{ $latest->destination }}</p>
            <p><strong>Date:</strong> {{ $latest->created_at->format('Y-m-d') }}</p>
            <p><strong>Status:</strong> {{ $latest->status }}</p>
            <p><strong>Crops:</strong></p>
            <ul>
                @foreach($latest->boxes as $box)
                    <li>{{ $box->crop }} — {{ $box->quantity }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endisset
</div>
@endsection
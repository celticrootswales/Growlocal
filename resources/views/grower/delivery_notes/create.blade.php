@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="fw-bold text-success mb-4">➕ Create New Delivery Note</h1>

    <!-- Delivery Note Form -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light">
            <h5 class="mb-0">Add a New Delivery Note</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('grower.delivery-notes.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf

                <div id="crop-entries">
                    <div class="crop-entry mb-3">
                        <label class="form-label">Crop</label>
                        <input type="text" class="form-control" name="crops[]" required>
                        <label class="form-label mt-2">Quantity (kg/Units)</label>
                        <input type="number" class="form-control" name="quantities[]" required>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3" id="add-entry">➕ Add Another Box</button>

                <div class="mb-3">
                    <label class="form-label">Destination</label>
                    <select class="form-control" name="destination" required>
                        <option value="">Select Destination</option>
                        <option value="Castle Howell">Castle Howell</option>
                        <option value="Bishop Fruit and Veg">Bishop Fruit and Veg</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Attach Invoice</label>
                    <input type="file" class="form-control" name="invoice">
                </div>

                <button type="submit" class="btn btn-success">Submit Delivery Note</button>
            </form>

            <script>
                document.getElementById('add-entry').addEventListener('click', function () {
                    const container = document.getElementById('crop-entries');
                    const entry = document.createElement('div');
                    entry.classList.add('crop-entry', 'mb-3');
                    entry.innerHTML = `
                        <label class="form-label">Crop</label>
                        <input type="text" class="form-control" name="crops[]" required>
                        <label class="form-label mt-2">Quantity (kg/Units)</label>
                        <input type="number" class="form-control" name="quantities[]" required>
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-entry">Remove</button>
                    `;
                    container.appendChild(entry);
                });

                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-entry')) {
                        e.target.closest('.crop-entry').remove();
                    }
                });
            </script>
        </div>
    </div>

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
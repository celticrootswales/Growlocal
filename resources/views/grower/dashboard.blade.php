@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="fw-bold text-success">📦 Delivery Notes</h1>
            <p class="text-muted">Manage and track your deliveries to distribution points.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Delivery Note Form -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light">
            <h5 class="mb-0">Add a New Delivery Note</h5>
        </div>
        <div class="card-body">
            <!-- Delivery Note Form -->
            <form action="{{ route('grower.delivery-notes.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf

                <div id="crop-entries">
                    <div class="crop-entry mb-3">
                        <label for="crop" class="form-label">Crop</label>
                        <input type="text" class="form-control" name="crops[]" required>
                        <label class="form-label mt-2">Quantity (kg/Units)</label>
                        <input type="number" class="form-control" name="quantities[]" required>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mb-3" id="add-entry">➕ Add Another Box</button>

                <div class="mb-3">
                    <label for="destination" class="form-label">Destination</label>
                    <select class="form-control" name="destination" required>
                        <option value="">Select Destination</option>
                        <option value="Castle Howell">Castle Howell</option>
                        <option value="Bishop Fruit and Veg">Bishop Fruit and Veg</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="invoice" class="form-label">Attach Invoice</label>
                    <input type="file" class="form-control" name="invoice">
                </div>

                <button type="submit" class="btn btn-success">Submit Delivery Notes</button>
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

    <!-- Existing Notes -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">🗃 Existing Delivery Notes</h5>
        </div>
        <div class="card-body p-0">
            @if($notes->isEmpty())
                <div class="p-4 text-center text-muted">
                    No delivery notes found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Crop</th>
                                <th>Quantity</th>
                                <th>Destination</th>
                                <th>Trace #</th>
                                <th>Status</th>
                                <th>Invoice</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notes as $note)
                            <tr>
                                <td>{{ $note->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($note->boxes as $box)
                                            <li>{{ $box->crop }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($note->boxes as $box)
                                            <li>{{ $box->quantity }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>{{ $note->destination }}</td>
                                <td>{{ $note->traceability_number }}</td>
                                <td>
                                    @if($note->status === 'Delivered')
                                        <span class="badge bg-success">✔ Delivered</span>
                                    @else
                                        <span class="badge bg-warning text-dark">⏳ Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($note->invoice_path)
                                        <a href="{{ asset('storage/' . $note->invoice_path) }}" target="_blank">📎 View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="d-flex gap-2">
                                    <a href="{{ url('/grower/pdf/delivery-note/' . $note->id) }}" class="btn btn-sm btn-outline-secondary">PDF</a>
                                    <a href="{{ url('/grower/pdf/label/' . $note->id) }}" class="btn btn-sm btn-outline-dark">Label</a>

                                    @if($note->status !== 'Delivered')
                                    <form action="{{ url('/grower/delivery-notes/' . $note->id . '/deliver') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Mark Delivered</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
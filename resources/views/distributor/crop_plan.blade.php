@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-success mb-4">🌾 Distributor Crop Plan</h1>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Add Crop Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            Add New Crop to Season Plan
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('distributor.crop-plan.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Crop Name</label>
                        <input type="text" name="crop_name" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <select name="unit" class="form-select" required>
                            <option value="kg">kg</option>
                            <option value="ea">Unit</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Expected Quantity</label>
                        <input type="number" name="expected_quantity" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Price per Unit (£)</label>
                        <input type="number" name="price_per_unit" step="0.01" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Assign to Grower</label>
                        <select name="grower_id" class="form-select" required>
                            <option value="">Select Grower</option>
                            @foreach($growers as $grower)
                                <option value="{{ $grower->id }}">{{ $grower->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Week (Start Date)</label>
                        <input type="date" name="week" class="form-control" required>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success mt-3">➕ Add Crop</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Crop Plan Table --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Seasonal Crop Plan Overview</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover small mb-0">
                <thead class="table-success">
                    <tr>
                        <th>🌱 Crop</th>
                        <th>📦 Unit</th>
                        <th>📈 Expected Qty</th>
                        <th>💰 Price / Unit</th>
                        <th>📅 Week</th>
                        <th>👩‍🌾 Grower</th>
                        <th>🌿 Commitments</th>
                        <th>⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>{{ $plan->crop_name }}</td>
                            <td>{{ $plan->unit === 'ea' ? 'Unit' : 'kg' }}</td>
                            <td>{{ $plan->expected_quantity }}</td>
                            <td>£{{ number_format($plan->price_per_unit, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($plan->week)->format('d M Y') }}</td>
                            <td>{{ $plan->grower->name ?? '-' }}</td>
                            <td>
                                @forelse($plan->commitments as $commitment)
                                    <div>
                                        <strong>{{ $commitment->grower->name }}</strong> →
                                        {{ $commitment->quantity }} {{ $plan->unit }}
                                    </div>
                                @empty
                                    <span class="text-muted">No commitments yet</span>
                                @endforelse
                            </td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-outline-primary mb-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $plan->id }}">
                                    ✏️ Edit
                                </button>

                                <!-- Delete Button -->
                                <form method="POST" action="{{ route('distributor.crop-plan.delete', $plan->id) }}" class="d-inline" onsubmit="return confirm('Delete this crop plan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">🗑️</button>
                                </form>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $plan->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $plan->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('distributor.crop-plan.update', $plan->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit {{ $plan->crop_name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Expected Quantity</label>
                                                        <input type="number" name="expected_quantity" class="form-control" value="{{ $plan->expected_quantity }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Price per Unit (£)</label>
                                                        <input type="number" name="price_per_unit" step="0.01" class="form-control" value="{{ $plan->price_per_unit }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No crops in the plan yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
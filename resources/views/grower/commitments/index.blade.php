@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-success mb-4">🌿 Your Crop Commitments</h1>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Section 1: Available Crops from Distributors --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-light">
            <strong>📦 Available Crops from Distributors ({{ $latestYear ?? date('Y') }})</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover small mb-0">
                <thead class="table-success">
                    <tr>
                        <th>Crop</th>
                        <th>Distributor</th>
                        <th>Amount Needed</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Term</th>
                        <th>Commit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($availableNeeds as $need)
                        <tr>
                            <td>{{ $need->cropOffering->icon ?? '' }} {{ $need->cropOffering->crop_name ?? '-' }}</td>
                            <td>{{ $need->distributor->name }}</td>
                            <td>{{ $need->desired_quantity }}</td>
                            <td>{{ $need->cropOffering->unit ?? '-' }}</td>
                            <td>£{{ number_format($need->cropOffering->default_price ?? 0, 2) }}</td>
                            <td>{{ $need->cropOffering->term ?? '-' }}</td>
                            <td>
                                <!-- Commitment Form -->
                                <form method="POST" action="{{ route('grower.commitments.store') }}">
                                    @csrf
                                    <input type="hidden" name="distributor_crop_need_id" value="{{ $need->id }}">
                                    <input type="number" name="quantity" step="0.01" min="0.1" class="form-control" placeholder="Enter quantity" required>
                                    <button type="submit" class="btn btn-sm btn-success mt-1">Commit</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted text-center">No crop needs have been published by distributors yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section 2: Your Submitted Commitments --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-success text-white">
            <strong>✅ Your Submitted Commitments ({{ $latestYear ?? date('Y') }})</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Crop</th>
                        <th>Distributor</th>
                        <th>Committed Amount</th>
                        <th>Unit</th>
                        <th>Term</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($commitments as $commitment)
                        <tr>
                            <td>{{ $commitment->cropOffering->crop_name ?? '-' }}</td>
                            <td>{{
                                    optional(optional($commitment->distributorNeed)->distributor)->business_name
                                    ?? optional(optional($commitment->distributorNeed)->distributor)->name
                                    ?? '-'
                                }}
                            </td>
                            <td>{{ $commitment->committed_quantity }} {{ $commitment->cropOffering->unit ?? '' }}</td>
                            <td>{{ $commitment->cropOffering->unit ?? '-' }}</td>
                            <td>{{ $commitment->cropOffering->term ?? '-' }}</td>
                            <td>
                                <!-- Edit -->
                                <a href="{{ route('grower.commitments.edit', $commitment->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                <!-- Delete -->
                                <form action="{{ route('grower.commitments.destroy', $commitment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">You have not made any commitments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="text-end">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">← Back to Dashboard</a>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- Banner/Header --}}
    <div class="rounded-4 p-4 mb-4"
        style="background: linear-gradient(90deg,#53c7fa 0%,#38e4b0 100%);color:#fff;">
        <div class="d-flex align-items-center">
            <span style="font-size:2.2rem;" class="me-3">🌿</span>
            <div>
                <h1 class="fw-bold mb-1" style="font-size:2.2rem;">Your Crop Commitments</h1>
                <div class="fs-5 fw-normal" style="opacity:0.85;">
                    Manage your market commitments and see new opportunities from distributors.
                </div>
            </div>
        </div>
    </div>


    {{-- Section 2: Your Submitted Commitments --}}
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-header bg-white border-bottom-0 fw-semibold fs-5 rounded-top-4">
            ✅ Your Submitted Commitments ({{ $latestYear ?? date('Y') }})
        </div>
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead class="bg-light">
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
                        <td>
                            <span class="fw-semibold">{{ $commitment->cropOffering->crop_name ?? '-' }}</span>
                        </td>
                        <td>
                            {{
                                optional(optional($commitment->distributorNeed)->distributor)->business_name
                                ?? optional(optional($commitment->distributorNeed)->distributor)->name
                                ?? '-'
                            }}
                        </td>
                        <td>
                            <span class="fw-medium">{{ $commitment->committed_quantity }}</span>
                            <span class="text-muted">{{ $commitment->cropOffering->unit ?? '' }}</span>
                        </td>
                        <td>{{ $commitment->cropOffering->unit ?? '-' }}</td>
                        <td>{{ $commitment->cropOffering->term ?? '-' }}</td>
                        <td>
                            @if (!$commitment->cropOffering->is_locked)
                                <a href="{{ route('grower.commitments.edit', $commitment->id) }}" class="btn btn-outline-primary btn-sm rounded-pill mb-1">Edit</a>
                                <form action="{{ route('grower.commitments.destroy', $commitment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm rounded-pill mb-1">Delete</button>
                                </form>
                            @else
                                <span class="text-muted">🔒 Locked</span>
                            @endif
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

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('grower.commitments.index') }}"
      class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-0">Distributor</label>
            <select name="distributor" class="form-select">
                <option value="">All</option>
                @foreach($grower->distributors as $dist)
                    <option value="{{ $dist->id }}"
                        {{ request('distributor') == $dist->id ? 'selected' : '' }}>
                        {{ $dist->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0">Crop</label>
            <input type="text" name="crop" class="form-control"
                   placeholder="e.g. Carrots" value="{{ request('crop') }}">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary">Filter</button>
        </div>
    </form>

    {{-- Section 1: Available Crops from Distributors --}}
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-header bg-white border-bottom-0 fw-semibold fs-5 rounded-top-4">
            📦 Available Crops from Distributors ({{ $latestYear ?? date('Y') }})
        </div>
        <div class="card-body p-0">
            <table class="table table-striped align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Crop</th>
                        <th>Distributor</th>
                        <th>Remaining</th>
                        <th>Committed</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Term</th>
                        <th>Commit</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($availableNeeds as $need)
                    <tr>
                        <td>
                            <span class="me-1">{{ $need->cropOffering->icon ?? '' }}</span>
                            <span class="fw-semibold">{{ $need->cropOffering->crop_name ?? '-' }}</span>
                        </td>

                        <td>{{ $need->distributor->name }}</td>
                        <td class="{{ $need->remaining_quantity < 0 ? 'text-danger fw-bold' : 'fw-medium text-success' }}">
                            {{ $need->remaining_quantity }}
                            @if($need->remaining_quantity < 0)
                                <div class="text-danger small">Over-committed</div>
                            @endif
                        </td>
                        <td class="text-muted small">
                            {{ number_format($need->committed_total ?? 0, 2) }}
                        </td>

                        <td>{{ $need->cropOffering->unit ?? '-' }}</td>
                        <td>£{{ number_format($need->cropOffering->default_price ?? 0, 2) }}</td>
                        <td>{{ $need->cropOffering->term ?? '-' }}</td>
                        <td>
                            @if (!$need->cropOffering->is_locked)
                                <form method="POST" action="{{ route('grower.commitments.store') }}">
                                    @csrf
                                    <input type="hidden" name="distributor_crop_need_id" value="{{ $need->id }}">
                                    <input type="number" name="quantity" step="0.01" min="0.1" class="form-control form-control-sm mb-1" placeholder="Qty" required>
                                    <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-3">Commit</button>
                                </form>
                            @else
                                <span class="text-muted">🔒 Locked</span>
                            @endif
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

    

    <div class="text-end">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill">← Back to Dashboard</a>
    </div>
</div>
@endsection
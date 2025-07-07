@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <div class="rounded-4 p-4 mb-4" style="background: linear-gradient(90deg, #53c7fa 0%, #38e4b0 100%); color: #fff;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
                    <span style="font-size:2.2rem;">🌽</span> Crop Offerings
                </h1>
                <div class="fs-5 fw-normal" style="opacity:0.85;">Manage your yearly crop plans and allocations</div>
            </div>
            <button class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="border-radius: 2rem;" data-bs-toggle="modal" data-bs-target="#addOfferingModal">
                + Add New Crop Offering
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-auto">
            <div class="p-3 bg-light rounded shadow-sm text-center">
                <span class="fw-bold fs-3 text-primary">{{ $offerings->count() }}</span>
                <div class="text-muted small">Total Offerings</div>
            </div>
        </div>
        <div class="col-auto">
            <div class="p-3 bg-light rounded shadow-sm text-center">
                <span class="fw-bold fs-3 text-success">{{ $distributors->count() }}</span>
                <div class="text-muted small">Active Distributors</div>
            </div>
        </div>
    </div>

    {{-- Lock/Unlock All for Selected Year --}}
    <div class="mb-4">
        <form method="POST" action="{{ route('admin.crop-offerings.toggle-lock-year', ['year' => $year]) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn {{ $areOfferingsLocked ? 'btn-warning' : 'btn-secondary' }}">
                {{ $areOfferingsLocked ? '🔓 Unlock All for ' . $year : '🔒 Lock All for ' . $year }}
            </button>
        </form>
    </div>



    {{-- Create Crop Offering Form --}}
    <div class="modal fade" id="addOfferingModal" tabindex="-1" aria-labelledby="addOfferingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:1.25rem;">
            <div class="modal-header" style="background:linear-gradient(90deg,#53c7fa,#38e4b0);border-radius:1.25rem 1.25rem 0 0;">
                <h5 class="modal-title text-white fw-bold" id="addOfferingModalLabel">Add New Crop Offering</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.crop-offerings.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label">Crop Name</label>
                        <input type="text" name="crop_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit</label>
                        <select name="unit" class="form-select" required>
                            <option value="kg">kg</option>
                            <option value="ea">Unit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (£)</label>
                        <input type="number" step="0.01" name="default_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount Needed</label>
                        <input type="number" step="0.01" name="amount_needed" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Term</label>
                        <select name="term" class="form-select">
                            <option value="">Select</option>
                            <option value="Autumn">Autumn</option>
                            <option value="Spring">Spring</option>
                            <option value="Summer">Summer</option>
                            <option value="Food and Fun">Food and Fun</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign to Distributors</label>
                        <select name="distributors[]" class="form-select" multiple>
                            @foreach($distributors as $distributor)
                                <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl or ⌘ to select multiple.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius:0 0 1.25rem 1.25rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success px-4">Add Offering</button>
                </div>
            </form>
        </div>
    </div>
</div>

    {{-- Filters --}}
    <form method="GET" class="row g-2 align-items-end mb-4">
        <div class="col-auto">
            <label class="form-label mb-0">Distributor</label>
            <select name="distributor" class="form-select">
                <option value="">All</option>
                @foreach($distributors as $dist)
                    <option value="{{ $dist->id }}" {{ request('distributor') == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-0">Year</label>
            <input type="number" name="year" value="{{ request('year', date('Y')) }}" class="form-control" min="2020">
        </div>
        <div class="col-auto">
            <label class="form-label mb-0">Term</label>
            <select name="term" class="form-select">
                <option value="">All</option>
                @foreach(['Spring', 'Summer', 'Autumn', 'Food and Fun'] as $term)
                    <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>{{ $term }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary" type="submit">Filter</button>
        </div>
    </form>

    {{-- Existing Offerings Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($offerings->count())
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Crop</th>
                            <th>Year</th>
                            <th>Term</th>
                            <th>Amount</th>
                            <th>Unit</th>
                            <th>Price (£)</th>
                            <th>Distributors</th>
                            <th>Status</th>
                            <th>Locked?</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($offerings as $offering)
                        <tr>
                            <td class="fw-bold">{{ $offering->icon ?? '🌱' }} {{ $offering->crop_name }}</td>
                            <td>{{ $offering->year }}</td>
                            <td>
                                <span class="badge 
                                    @if($offering->term === 'Autumn') bg-warning 
                                    @elseif($offering->term === 'Spring') bg-success 
                                    @elseif($offering->term === 'Summer') bg-info 
                                    @else bg-secondary 
                                    @endif">
                                    {{ $offering->term ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $offering->amount_needed }}</td>
                            <td>{{ $offering->unit }}</td>
                            <td>£{{ number_format($offering->default_price, 2) }}</td>
                            <td>
                                @foreach($offering->distributors as $dist)
                                    <span class="badge rounded-pill bg-light text-dark border me-1">{{ $dist->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($offering->submitted_to_distributors)
                                    <span class="badge bg-info">Submitted</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($offering->is_locked)
                                    <span class="badge bg-danger">Locked</span>
                                @else
                                    <span class="badge bg-success">Open</span>
                                @endif
                            </td>
                            <td>
                                @if(!$offering->submitted_to_distributors)
                                <form method="POST" action="{{ route('admin.offerings.submit', $offering->id) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm rounded-pill shadow-sm" title="Submit to Distributors">
                                        <i class="bi bi-send"></i>
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.crop-offerings.edit', $offering->id) }}" class="btn btn-primary btn-sm rounded-pill shadow-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.crop-offerings.destroy', $offering->id) }}" class="d-inline" onsubmit="return confirm('Delete this crop offering?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm rounded-pill shadow-sm" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="p-4 text-center text-muted">No crop offerings found.</div>
            @endif
        </div>
    </div>
</div>
@endsection
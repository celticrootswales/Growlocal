@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mt-3 fw-bold">Yearly Crop Offerings</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Create Crop Offering Form --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header header-blue text-white py-3">
            <strong>Add New Crop Offering</strong>
        </div>
        <form method="POST" action="{{ route('admin.crop-offerings.store') }}" class="card-body row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Crop Name</label>
                <input type="text" name="crop_name" class="form-control" required>
            </div>
            <div class="col-md-1">
                <label class="form-label">Unit</label>
                <select name="unit" class="form-select" required>
                    <option value="kg">kg</option>
                    <option value="ea">Unit</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Year</label>
                <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
            </div>
            <div class="col-md-1">
                <label class="form-label">Price (£)</label>
                <input type="number" step="0.01" name="default_price" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Amount Needed</label>
                <input type="number" step="0.01" name="amount_needed" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Term</label>
                <select name="term" class="form-select">
                    <option value="">Select</option>
                    <option value="Autumn">Autumn</option>
                    <option value="Spring">Spring</option>
                    <option value="Summer">Summer</option>
                    <option value="Food and Fun">Food and Fun</option>
                </select>
            </div>
            <div class="col-md-9">
                <label class="form-label">Assign to Distributors</label>
                <select name="distributors[]" class="form-select" multiple>
                    @foreach($distributors as $distributor)
                        <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl or ⌘ to select multiple.</small>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Add Crop Offering</button>
            </div>
        </form>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.crop-offerings.index') }}" class="row g-3 mt-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Distributor</label>
            <select name="distributor" class="form-select">
                <option value="">All</option>
                @foreach($distributors as $distributor)
                    <option value="{{ $distributor->id }}" {{ request('distributor') == $distributor->id ? 'selected' : '' }}>
                        {{ $distributor->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Term</label>
            <select name="term" class="form-select">
                <option value="">All</option>
                @foreach(['Autumn', 'Spring', 'Summer', 'Food and Fun'] as $term)
                    <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>{{ $term }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Year</label>
            <input type="number" name="year" value="{{ request('year') }}" class="form-control">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
    </form>

    {{-- Existing Offerings Table --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header header-blue text-white py-3">
            <h5 class="mb-0">Existing Crop Offerings</h5>
        </div>
        <div class="card-body p-0">
            @if($offerings->isEmpty())
                <p class="p-3 text-muted">No crop offerings yet.</p>
            @else
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Crop</th>
                            <th>Unit</th>
                            <th>Year</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Term</th>
                            <th>Distributors</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offerings as $offering)
                            <tr>
                                <td>{{ $offering->crop_name }}</td>
                                <td>{{ $offering->unit }}</td>
                                <td>{{ $offering->year }}</td>
                                <td>£{{ number_format($offering->default_price, 2) }}</td>
                                <td>{{ $offering->amount_needed }} {{ $offering->unit }}</td>
                                <td>
                                    <span class="badge
                                        @if($offering->term === 'Autumn') bg-warning
                                        @elseif($offering->term === 'Spring') bg-success
                                        @elseif($offering->term === 'Summer') bg-info
                                        @elseif($offering->term === 'Food and Fun') bg-primary
                                        @else bg-secondary
                                        @endif">
                                        {{ $offering->term ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @foreach($offering->distributors as $dist)
                                        <span class="badge bg-dark">{{ $dist->name }}</span>
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
                                    @if (!$offering->submitted_to_distributors)
                                        <form method="POST" action="{{ route('admin.offerings.submit', $offering->id) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success">Submit</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.crop-offerings.edit', $offering->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                    <form method="POST" action="{{ route('admin.crop-offerings.destroy', $offering->id) }}"
                                          class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
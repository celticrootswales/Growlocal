@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-success mb-4">🏬 Distributor Dashboard</h1>

    {{-- ✅ Flash message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 🔍 Filters and Export --}}
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search Trace #</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>

            <div class="col-md-auto">
                <button class="btn btn-outline-success">🔍 Filter</button>
            </div>
            <div class="col-md-auto">
                <button type="submit" name="export" value="csv" class="btn btn-outline-secondary">
                    ⬇ Export CSV
                </button>
            </div>
        </div>
    </form>

    {{-- 📦 Delivery Notes Table --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Assigned Delivery Notes</h5>
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
                                <th>Trace #</th>
                                <th>Grower</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notes as $note)
                                <tr>
                                    <td>{{ $note->traceability_number }}</td>
                                    <td>{{ $note->user->name }}</td>
                                    <td>
                                        @if($note->recalled)
                                            <span class="badge bg-danger">Recalled</span>
                                        @elseif($note->status === 'Delivered')
                                            <span class="badge bg-success">Delivered</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $note->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ url('/grower/pdf/delivery-note/' . $note->id) }}" class="btn btn-sm btn-outline-primary">📄 PDF</a>
                                        <a href="{{ url('/grower/pdf/label/' . $note->id) }}" class="btn btn-sm btn-outline-dark">🏷 Label</a>
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

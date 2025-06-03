@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4 text-success">⚠ Recalled Delivery Notes</h1>

    {{-- ✅ Flash message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ Filter --}}
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="🔍 Filter by Trace #">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-outline-success">Search</button>
            </div>
        </div>
    </form>

    @if($notes->isEmpty())
        <div class="alert alert-info">
            No recalled delivery notes found{{ request('search') ? ' for this search' : '' }}.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Trace #</th>
                        <th>Grower</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notes as $note)
                        <tr>
                            <td><strong>{{ $note->traceability_number }}</strong></td>
                            <td>{{ $note->user->name }}</td>
                            <td>{{ $note->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if($note->recall_acknowledged)
                                    <span class="badge bg-success">✔ Acknowledged</span>
                                @else
                                    <span class="badge bg-danger">❗ Unacknowledged</span>
                                @endif
                            </td>
                            <td>{{ optional($note->recall)->reason ?? 'No reason provided' }}</td>
                            <td class="d-flex gap-2 flex-wrap">
                                <a href="{{ url('/grower/pdf/delivery-note/' . $note->id) }}" class="btn btn-sm btn-outline-secondary">📄 PDF</a>
                                <a href="{{ url('/grower/pdf/label/' . $note->id) }}" class="btn btn-sm btn-outline-dark">🏷 Label</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 fw-bold text-danger">⚠ Recalled Delivery Batches</h1>

    <form method="GET" action="{{ route('admin.recalls') }}" class="row g-3 align-items-center mb-4">
        <div class="col-auto">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Trace #">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.recalls') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($notes->isEmpty())
        <div class="alert alert-info">No recalled batches at the moment.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Trace #</th>
                        <th>Grower</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notes as $note)
                        <tr>
                            <td>{{ $note->traceability_number }}</td>
                            <td>{{ $note->user->name ?? 'Unknown' }}</td>
                            <td>{{ $note->destination }}</td>
                            <td>{{ $note->created_at->format('Y-m-d') }}</td>
                            <td>{{ $note->recall->reason ?? 'N/A' }}</td>
                            <td>
                                <form action="{{ route('admin.recall.remove', $note->id) }}" method="POST" onsubmit="return confirm('Remove this recall?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        ❌ Remove Recall
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
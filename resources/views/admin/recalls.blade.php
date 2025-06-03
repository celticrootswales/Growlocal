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

    @if($recalls->isEmpty())
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
                        {{-- other headers --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($recalls as $recall)
                        <tr>
                            <td>{{ $recall->traceability_number }}</td>
                            <td>{{ $recall->user->name }}</td>
                            <td>{{ $recall->destination ?? 'N/A' }}</td>
                            <td>{{ $recall->created_at->format('d M Y') }}</td>
                            <td>{{ $recall->recall_reason ?? 'N/A' }}</td>
                            {{-- other fields --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
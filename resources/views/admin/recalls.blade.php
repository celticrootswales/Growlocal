@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Banner --}}
    <div class="rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between"
         style="background: linear-gradient(90deg, #fda769 0%, #ff5858 100%); color: #fff;">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
                <span style="font-size:2.2rem;">⚠️</span> Recalled Delivery Batches
            </h1>
            <div class="fs-5 fw-normal" style="opacity:0.85;">All products currently recalled by growers or distributors</div>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.recalls') }}" class="row g-3 align-items-center mb-4">
        <div class="col-auto">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Trace #">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.recalls') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    {{-- Flash Success --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- No Recalls --}}
    @if($recalls->isEmpty())
        <div class="alert alert-info rounded-4 shadow-sm py-4 text-center">
            <span class="fs-4 fw-semibold">No recalled batches at the moment.</span>
        </div>
    @else
        <div class="table-responsive rounded-4 shadow-sm">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Trace #</th>
                        <th>Grower</th>
                        <th>Destination</th>
                        <th>Date</th>
                        <th>Reason</th>
                        {{-- Add more headers if needed --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($recalls as $recall)
                        <tr>
                            <td class="fw-bold">{{ $recall->traceability_number }}</td>
                            <td>{{ $recall->user->business_name ?? $recall->user->name ?? '-' }}</td>
                            <td>{{ $recall->destination ?? $recall->distributor->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    {{ $recall->created_at->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                    {{ $recall->recall_reason ?? 'N/A' }}
                                </span>
                            </td>
                            {{-- More fields/actions if needed --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
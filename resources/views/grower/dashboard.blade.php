@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-success mb-4"> Grower Dashboard</h1>

    {{-- ✅ Flash message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ Recall Alert --}}
    @if($recalled->count())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>⚠ Recall Alert!</strong> One or more of your deliveries have been recalled:
            <ul class="mt-2 mb-0">
                @foreach($recalled as $note)
                    <li class="mb-2">
                        <strong>Trace #{{ $note->traceability_number }}</strong> — 
                        {{ optional($note->recall)->reason ?? 'No reason provided' }}

                        @if($note->recall_acknowledged)
                            <span class="badge bg-success ms-2">✔ Acknowledged</span>
                        @else
                            <form method="POST" action="{{ route('grower.recall.acknowledge', $note->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-light ms-2">
                                    Acknowledge
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ✅ Dashboard Stats --}}
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📦 Total Deliveries</h5>
                    <p class="display-6 fw-bold">{{ $notes->count() }}</p>
                    <small class="text-muted">All submitted delivery notes</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">⏳ Pending</h5>
                    <p class="display-6 fw-bold">{{ $notes->where('status', 'Pending')->count() }}</p>
                    <small class="text-muted">Awaiting delivery confirmation</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">⚠ Recalled</h5>
                    <p class="display-6 fw-bold">{{ $recalled->count() }}</p>
                    <small class="text-muted">Batches flagged by admin</small>
                </div>
            </div>
        </div>
    </div>

    @if($plans->isNotEmpty())
        <h4 class="mt-4">📅 Your Crop Plan</h4>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Week</th>
                    <th>Crop</th>
                    <th>Expected</th>
                    <th>Delivered</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($plan->week_start)->format('d M Y') }}</td>
                        <td>{{ $plan->crop }} ({{ $plan->unit }})</td>
                        <td>{{ $plan->expected_quantity }}</td>
                        <td>{{ $supplied[$plan->id] ?? 0 }}</td>
                        <td>
                            @php
                                $delivered = $supplied[$plan->id] ?? 0;
                            @endphp
                            @if($delivered >= $plan->expected_quantity)
                                ✅
                            @elseif($delivered > 0)
                                ⚠ Partial
                            @else
                                ❌ Not Supplied
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
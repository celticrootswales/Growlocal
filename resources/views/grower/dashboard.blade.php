@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- Header Banner --}}
    <div class="rounded-4 p-4 mb-4 d-flex flex-column flex-md-row align-items-center justify-content-between"
        style="background: linear-gradient(90deg,#53c7fa 0,#38e4b0 100%); color:#fff;">
        <div>
            <h1 class="fw-bold mb-1" style="font-size:2.2rem;">
                <span style="font-size:2.2rem;">👨‍🌾</span> Growers Dashboard
            </h1>
            <div class="fs-5 fw-normal" style="opacity:0.85;">Your farm at a glance</div>
        </div>
        <div class="ms-md-4 mt-4 mt-md-0"
            style="min-width:320px; background:rgba(255,255,255,0.15); border-radius:1.25rem;">
            <div class="d-flex align-items-center px-4 py-3">
                <div class="me-3" style="font-size:2.2rem;">📦</div>
                <div>
                    <div class="fw-semibold">Active Delivery Notes</div>
                    <div class="fs-2 fw-bold">{{ $notes->count() }}</div>
                    @if($recalled->count())
                        <div class="mt-2 text-warning-emphasis fw-semibold small">
                            {{ $recalled->count() }} delivery notes currently recalled!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-6">
            <div class="p-4 rounded-4 shadow-sm text-center bg-light border-0">
                <div class="fs-1 fw-bold text-primary mb-2">{{ $plans->count() }}</div>
                <div class="text-muted fw-semibold">Crop Plans</div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="p-4 rounded-4 shadow-sm text-center bg-light border-0">
                <div class="fs-1 fw-bold text-success mb-2">{{ $notes->count() }}</div>
                <div class="text-muted fw-semibold">Delivery Notes</div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="p-4 rounded-4 shadow-sm text-center bg-light border-0">
                <div class="fs-1 fw-bold text-danger mb-2">{{ $recalled->count() }}</div>
                <div class="text-muted fw-semibold">Recalled Notes</div>
            </div>
        </div>
        @if(isset($estimatedValue))
        <div class="mb-4 col-6">
            <div class="p-4 rounded-4 shadow-sm d-flex align-items-center" style="background:linear-gradient(90deg,#f2fbf7 0,#e3f7ed 100%);">
                <div class="me-4" style="font-size:2.2rem;">💰</div>
                <div>
                    <div class="fw-semibold text-success" style="font-size:1.1rem;">Estimated Value of Your Commitments ({{ date('Y') }})</div>
                    <div class="fs-2 fw-bold text-dark">£{{ number_format($estimatedValue, 2) }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Crop Plans & Weekly Progress --}}
    <div class="card mb-4 shadow-sm rounded-4">
        <div class="card-header bg-light fw-bold" style="border-radius: 1rem 1rem 0 0;">🌱 Weekly Crop Progress</div>
        <div class="card-body p-4">
            @if($plans->count())
                <div class="table-responsive">
                    <table class="table align-middle table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Crop</th>
                                <th>Week Starting</th>
                                <th>Planned Qty</th>
                                <th>Supplied Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>{{ $plan->crop }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->week_start)->format('d M Y') }}</td>
                                    <td>{{ $plan->planned_quantity ?? '-' }}</td>
                                    <td>
                                        {{ $supplied[$plan->id] ?? 0 }}
                                    </td>
                                    <td>
                                        @if(isset($supplied[$plan->id]) && $plan->planned_quantity)
                                            @if($supplied[$plan->id] >= $plan->planned_quantity)
                                                <span class="badge bg-success">On Target</span>
                                            @elseif($supplied[$plan->id] == 0)
                                                <span class="badge bg-warning">No Deliveries</span>
                                            @else
                                                <span class="badge bg-info">Partial</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">No Data</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">No crop plans found.</div>
            @endif
        </div>
    </div>

    {{-- Recent Delivery Notes --}}
    <div class="row g-4">
        <div class="col-md-12">
            <div class="card shadow-sm mb-3 h-100">
                <div class="card-header fw-bold bg-light" style="border-radius: 1rem 1rem 0 0;">🚚 Recent Deliveries</div>
                <div class="card-body">
                    @if($notes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($notes->take(6) as $note)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="fw-bold">Trace #{{ $note->traceability_number }}</span>
                                        <span class="text-muted ms-1">{{ $note->created_at->format('d M Y') }}</span>
                                        <span class="badge bg-light text-dark border ms-2">{{ ucfirst($note->status) }}</span>
                                    </span>
                                    <span>
                                        @if($note->boxes->count())
                                            <span class="fw-normal small">
                                                @foreach($note->boxes as $box)
                                                    {{ $box->crop }} ({{ $box->quantity }}){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </span>
                                        @else
                                            <span class="text-muted small">No crops</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No recent deliveries found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
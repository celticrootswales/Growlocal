@extends('layouts.app')
@section('content')
<div class="container py-5">

    {{-- Header Banner --}}
    <div class="rounded-4 p-4 mb-4 d-flex flex-column flex-md-row align-items-center justify-content-between"
     style="background: linear-gradient(90deg, #53c7fa 0%, #38e4b0 100%); color: #fff;">
    <div>
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
            <span style="font-size:2.2rem; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.12))">📈</span> Dashboard Overview
        </h1>
        <div class="fs-5 fw-normal" style="opacity:0.85;">Get a quick summary of all key stats and actions</div>
    </div>
    <div class="ms-md-4 mt-4 mt-md-0"
         style="min-width:320px; background:rgba(255,255,255,0.15); border-radius:1.25rem;">
        <div class="d-flex align-items-center px-4 py-3">
            <div class="me-3" style="font-size:2.2rem; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.12))">💷</div>
            <div>
                
                <div class="fw-semibold">Potential Income for Welsh Grower <span class="fs-6">({{ $selectedYear }})</span></div>
                <div class="fs-2 fw-bold">£{{ number_format($totalPotentialIncome, 2) }}</div>
                <div class="mt-2">
                    @foreach($incomeByTerm as $term => $amount)
                        <span class="badge bg-light text-dark px-3 py-2 me-2 fs-6 rounded-pill shadow-sm"
                              style="background:rgba(255,255,255,0.85);color:#444;">
                            {{ $term }}: £{{ number_format($amount, 2) }}
                        </span>
                    @endforeach
                </div>
                @if($cropsWithoutAmount)
                    <div class="mt-2 small text-warning-emphasis fw-semibold">
                        {{ $cropsWithoutAmount }} crops missing "amount needed"
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

    {{-- Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 shadow-sm text-center bg-light border-0">
                <div class="fs-1 fw-bold text-primary mb-2">{{ $growersCount }}</div>
                <div class="text-muted fw-semibold">Total Growers</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 shadow-sm text-center bg-light border-0">
                <div class="fs-1 fw-bold text-success mb-2">{{ $distributorsCount }}</div>
                <div class="text-muted fw-semibold">Active Distributors</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 shadow-sm text-center bg-light border-0">
                <div class="fs-1 fw-bold text-info mb-2">{{ $offeringsCount }}</div>
                <div class="text-muted fw-semibold">Crop Offerings ({{ $selectedYear }})</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="p-4 rounded-4 shadow-sm text-center bg-light border-0">
                <div class="fs-1 fw-bold text-warning mb-2">{{ $deliveriesCount }}</div>
                <div class="text-muted fw-semibold">Deliveries</div>
            </div>
        </div>
    </div>



    {{-- Recent Deliveries --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3 h-100">
                <div class="card-header fw-bold bg-light" style="border-radius: 1rem 1rem 0 0;">🚚 Recent Deliveries</div>
                <div class="card-body">
                    @if($recentNotes->count())
                        <ul class="list-group list-group-flush">
                            @foreach($recentNotes as $note)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="fw-bold">{{ $note->crop ?? '—' }}</span>
                                        <span class="text-muted ms-1">{{ $note->quantity ?? '' }} {{ $note->unit ?? '' }}</span>
                                    </span>
                                    <span>
                                        <span class="text-secondary small">{{ $note->user->business_name ?? $note->user->name }}</span>
                                        <span class="badge bg-light text-dark border ms-2">{{ $note->created_at->format('d M Y') }}</span>
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
        <div class="col-md-6">
            <div class="card shadow-sm mb-3 h-100">
                <div class="card-header fw-bold bg-light" style="border-radius: 1rem 1rem 0 0;">⚠️ Active Recalls</div>
                <div class="card-body">
                    @if($activeRecalls->count())
                        <ul class="list-group list-group-flush">
                            @foreach($activeRecalls as $recall)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <span class="fw-bold">{{ $recall->crop ?? '—' }}</span>
                                        <span class="text-danger ms-1">Recalled</span>
                                    </span>
                                    <span>
                                        <span class="text-secondary small">{{ $recall->user->business_name ?? $recall->user->name }}</span>
                                        <span class="badge bg-danger ms-2">{{ $recall->created_at->format('d M Y') }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No active recalls found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
<style>
.card-header.bg-light {
    background: #f7fafc !important;
}
.list-group-item {
    border: none !important;
}
</style>
</div>
@endsection
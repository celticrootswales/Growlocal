@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold mb-4">Admin Dashboard</h1>

    {{-- Metrics Section --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Total Potential Income</h5>
                    <p class="card-text fs-4 fw-bold">£{{ number_format($totalPotentialIncome, 2) }}</p>
                    <small class="text-muted">Based on current crop offerings</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">Offerings Missing Amount</h5>
                    <p class="card-text fs-4 fw-bold">{{ $cropsWithoutAmount }}</p>
                    <small class="text-muted">These offerings have no quantity set</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Offerings</h5>
                    <p class="card-text fs-4 fw-bold">{{ $totalOfferings }}</p>
                    <small class="text-muted">In {{ $selectedYear }}</small>
                </div>
            </div>
        </div>
    </div>


    {{-- Income by Term --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Income by Term</strong>
        </div>
        <div class="card-body">
            @if($incomeByTerm->isEmpty())
                <p class="text-muted">No term data available.</p>
            @else
                <ul class="list-group">
                    @foreach ($incomeByTerm as $term => $income)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $term }}</span>
                            <strong>£{{ number_format($income, 2) }}</strong>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Top Distributors --}}
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Top Distributors</strong></div>
        <div class="card-body">
            @if($topDistributors->isEmpty())
                <p class="text-muted">No distributor data available.</p>
            @else
                <ul class="list-group">
                    @foreach ($topDistributors as $dist)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $dist->name }}
                            <span class="badge bg-secondary">{{ $dist->offerings_count }} Offerings</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Recent Delivery Notes --}}
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Recent Delivery Notes</strong></div>
        <div class="card-body">
            @if($recentNotes->isEmpty())
                <p class="text-muted">No recent notes available.</p>
            @else
                <ul class="list-group">
                    @foreach ($recentNotes as $note)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>
                                {{ $note->traceability_number }} from {{ $note->user->name }} 
                                ({{ $note->created_at->format('d M Y') }})
                            </span>
                            <span class="text-muted">{{ $note->boxes->count() }} boxes</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Active Recalls --}}
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Active Recalls</strong></div>
        <div class="card-body">
            @if($activeRecalls->isEmpty())
                <p class="text-muted">No active recalls at the moment.</p>
            @else
                <ul class="list-group">
                    @foreach ($activeRecalls as $recall)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>
                                {{ $recall->traceability_number }} by {{ $recall->user->name }}
                            </span>
                            <span class="text-danger fw-bold">⚠️ Recalled</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
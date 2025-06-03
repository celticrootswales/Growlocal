@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-success mb-4">📋 Weekly Grower Estimates</h2>

    @forelse($weeklyPlans as $plan)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="mb-2">
                    {{ $plan->commitment->distributorCropNeed->cropOffering->emoji }}
                    {{ $plan->commitment->distributorCropNeed->cropOffering->name }}
                </h5>
                <p>
                    <strong>Week:</strong> {{ \Carbon\Carbon::parse($plan->week)->format('Y-m-d') }} <br>
                    <strong>Grower:</strong> {{ $plan->commitment->grower->name }} <br>
                    <strong>Target Qty:</strong> {{ $plan->expected_quantity }} {{ $plan->commitment->distributorCropNeed->cropOffering->unit }}
                </p>

                @if($plan->estimate)
                    <div class="alert alert-success mb-0">
                        <strong>Grower's Estimate:</strong> {{ $plan->estimate->estimated_quantity }} 
                        {{ $plan->commitment->distributorCropNeed->cropOffering->unit }}
                        <br>
                        <em>{{ $plan->estimate->notes }}</em>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        No estimate submitted by grower yet.
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">No weekly data available yet.</p>
    @endforelse
</div>
@endsection
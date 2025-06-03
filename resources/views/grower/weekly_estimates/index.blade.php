@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-success mb-4">📦 Weekly Delivery Estimates</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @forelse($plans as $plan)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="mb-1">
                    {{ $plan->commitment->distributorCropNeed->cropOffering->emoji }}
                    {{ $plan->commitment->distributorCropNeed->cropOffering->name }}
                </h5>
                <p class="mb-2">
                    Week: <strong>{{ \Carbon\Carbon::parse($plan->week)->format('Y-m-d') }}</strong> |
                    Expected: <strong>{{ $plan->expected_quantity }} {{ $plan->commitment->distributorCropNeed->cropOffering->unit }}</strong>
                </p>

                <form method="POST" action="{{ route('grower.weekly-estimates.store') }}">
                    @csrf
                    <input type="hidden" name="weekly_crop_plan_id" value="{{ $plan->id }}">

                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="number" name="estimated_quantity" class="form-control" placeholder="Estimated Quantity" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="notes" class="form-control" placeholder="Notes (optional)">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-success w-100">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <p class="text-muted">No weekly plans available yet.</p>
    @endforelse
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-success mb-4">📅 Weekly Crop Planning</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($commitments as $commitment)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                {{ $commitment->grower->name }} – {{ $commitment->distributorCropNeed->cropOffering->emoji }}
                {{ $commitment->distributorCropNeed->cropOffering->name }}
                ({{ $commitment->committed_quantity }} {{ $commitment->distributorCropNeed->cropOffering->unit }})
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.weekly-plans.store') }}">
                    @csrf
                    <input type="hidden" name="grower_crop_commitment_id" value="{{ $commitment->id }}">

                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Week</label>
                            <input type="date" name="week" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Expected Quantity</label>
                            <input type="number" name="expected_quantity" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-success w-100 mt-2">➕ Add</button>
                        </div>
                    </div>
                </form>

                <hr>

                <table class="table small table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Expected Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commitment->weeklyPlans as $plan)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($plan->week)->format('Y-m-d') }}</td>
                                <td>{{ $plan->expected_quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="fw-bold text-success mb-4">My Crop Plan</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($plans->isEmpty())
        <div class="alert alert-info">No crop plans assigned to you yet.</div>
    @else
        <table class="table table-bordered table-hover small">
            <thead class="table-success">
                <tr>
                    <th>📅 Week</th>
                    <th>🌾 Crop</th>
                    <th>📦 Unit</th>
                    <th>📈 Expected</th>
                    <th>💬 My Estimate</th>
                    <th>✅ Save</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($plan->week)->format('d M Y') }}</td>
                        <td>{{ $plan->crop_name }}</td>
                        <td>{{ $plan->unit === 'ea' ? 'Each (unit)' : strtoupper($plan->unit) }}</td>
                        <td>{{ $plan->expected_quantity }}</td>
                        <td>
                            <form method="POST" action="{{ route('grower.crop-plan.update', $plan->id) }}" class="d-flex">
                                @csrf
                                @method('PUT')
                                <input type="number" step="0.01" name="grower_estimate" class="form-control form-control-sm me-2" value="{{ $plan->grower_estimate }}">
                                <button type="submit" class="btn btn-sm btn-success">💾</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
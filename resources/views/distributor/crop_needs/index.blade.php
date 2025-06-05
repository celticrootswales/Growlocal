@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1>Your Assigned Crop Offerings</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label>Filter by Term:</label>
                <select name="term" class="form-control" onchange="this.form.submit()">
                    <option value="">All Terms</option>
                    <option value="Spring" {{ request('term') == 'Spring' ? 'selected' : '' }}>Spring</option>
                    <option value="Summer" {{ request('term') == 'Summer' ? 'selected' : '' }}>Summer</option>
                    <option value="Autumn" {{ request('term') == 'Autumn' ? 'selected' : '' }}>Autumn</option>
                </select>
            </div>
        </div>
    </form>

    @if($term)
        <p>Showing results for: <strong>{{ $term }}</strong></p>
    @endif

    @forelse($offerings as $cropOffering)
        <div class="card mb-4">
            <div class="card-body">
                <h5>{{ $cropOffering->icon ?? '🌱' }} {{ $cropOffering->crop_name }} ({{ $cropOffering->year }} - {{ $cropOffering->term }})</h5>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Distributor Need</th>
                            <th>Unit</th>
                            <th>Default Price</th>
                            <th>Growers</th>
                            <th>Committed</th>
                            <th>Missing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $cropOffering->amount_needed ?? 'Not set' }}</td>
                            <td>{{ $cropOffering->unit ?? '-' }}</td>
                            <td>£{{ number_format($cropOffering->default_price, 2) }}</td>
                            <td>
                                @php
                                    $commitments = $cropOffering->growerCommitments;
                                    $committedGrowers = $commitments
                                        ->pluck('grower')
                                        ->unique('id')
                                        ->filter(fn($grower) => !is_null($grower));
                                @endphp

                                @if($committedGrowers->isEmpty())
                                    <span class="text-muted">None yet</span>
                                @else
                                    <ul class="mb-0">
                                        @foreach($committedGrowers as $grower)
                                            <li>{{ $grower->business_name ?? $grower->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>
                                {{ $commitments->sum('committed_quantity') ?? 0 }}
                            </td>
                            <td>
                                @php
                                    $needed = $cropOffering->amount_needed ?? 0;
                                    $committed = $commitments->sum('committed_quantity') ?? 0;
                                    $remaining = max(0, $needed - $committed);
                                @endphp
                                {{ $remaining }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No crop offerings assigned.</div>
    @endforelse
</div>
@endsection
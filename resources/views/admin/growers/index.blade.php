@extends('layouts.app')
@section('content')
<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header Banner --}}
    <div class="rounded-4 p-4 mb-4" style="background: linear-gradient(90deg, #53c7fa 0%, #38e4b0 100%); color: #fff;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
                    <span style="font-size:2.2rem;">👨‍🌾</span> Manage Growers
                </h1>
                <div class="fs-5 fw-normal" style="opacity:0.85;">All your local farm partners in one place</div>
            </div>
            <button class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="border-radius: 2rem;" data-bs-toggle="modal" data-bs-target="#addGrowerModal">
                + Add New Grower
            </button>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <div class="p-3 bg-light rounded shadow-sm text-center">
                <span class="fw-bold fs-3 text-primary">{{ $growers->count() }}</span>
                <div class="text-muted small">Total Growers</div>
            </div>
        </div>
        <div class="col-auto">
            <div class="p-3 bg-light rounded shadow-sm text-center">
                <span class="fw-bold fs-3 text-success">{{ $distributors->count() }}</span>
                <div class="text-muted small">Active Distributors</div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.growers.index') }}" class="row g-2 align-items-end mb-4">
        <div class="col-auto">
            <label class="form-label mb-0">Distributor</label>
            <select name="distributor" class="form-select">
                <option value="">All</option>
                @foreach($distributors as $dist)
                    <option value="{{ $dist->id }}" {{ request('distributor') == $dist->id ? 'selected' : '' }}>{{ $dist->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary" type="submit">Filter</button>
        </div>
    </form>

    {{-- Growers Cards --}}
    <div class="row">
        @forelse($growers as $grower)
            <div class="col-md-4 mb-4">
                <div class="card shadow-lg border-0 h-100 grower-card position-relative" style="border-radius: 2rem;">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">

                        {{-- Avatar --}}
                        <div class="row">
                            <div class="col-md-3">
                                @if ($grower->logo)
                                    <img src="{{ asset('storage/' . $grower->logo) }}"
                                         alt="{{ $grower->business_name ?? $grower->name }} Logo"
                                         class="rounded-circle shadow"
                                         style="width:64px; height:64px; object-fit:cover; border:3px solid #f8fafc; margin-right: 16px;">
                                @else
                                    @php
                                        $source = $grower->business_name ?: $grower->name;
                                        $initials = collect(explode(' ', $source))
                                            ->map(fn($w) => strtoupper($w[0] ?? ''))
                                            ->take(2)
                                            ->join('');
                                        $colors = ['#7db1ff', '#fda769', '#97e284', '#f57bc7', '#ffe265', '#60e7ef', '#a8a8f7'];
                                        $color = $colors[$grower->id % count($colors)];
                                    @endphp
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                                         style="width:64px; height:64px; background:{{ $color }}; color:#fff; font-size:2rem; font-weight:bold; border:3px solid #f8fafc; margin-right: 16px;"
                                         title="{{ $source }}">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <div class="fw-bold fs-5 mb-0" style="letter-spacing:1px;">{{ $grower->business_name }}</div>
                            </div>
                        </div>

                        <div class="mb-2 mt-4">
                            <div class="text-muted small"> <i class="bi bi-user"></i> {{ $grower->name }}</div>
                            <i class="bi bi-envelope"></i> <span class="text-secondary">{{ $grower->email }}</span>
                        </div>

                        <div class="my-3">
                            <span class="fw-semibold">Distributors:</span>
                            @php
                                $pastelColors = [
                                    '#a3d9c9', '#ffd6a5', '#bdb2ff', '#ffadad', '#caffbf',
                                    '#ffd6e0', '#b5ead7', '#f8edeb', '#d0f4de', '#a0c4ff',
                                ];
                            @endphp
                            @foreach($grower->distributors as $dist)
                                @php
                                    $badgeColor = $pastelColors[$dist->id % count($pastelColors)];
                                @endphp
                                <span class="badge rounded-pill me-1"
                                      style="background: {{ $badgeColor }}; color: #; font-weight: 500; border:1px solid #eee;">
                                    {{ $dist->name }}
                                </span>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between gap-2 mt-auto">
                            <a href="{{ route('admin.growers.edit', $grower->id) }}" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.growers.destroy', $grower->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this grower?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm px-3 rounded-pill shadow-sm">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>No growers found.</p>
        @endforelse
    </div>
</div>

{{-- Modal for Add New Grower --}}
<div class="modal fade" id="addGrowerModal" tabindex="-1" aria-labelledby="addGrowerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:1.25rem;">
            <div class="modal-header" style="background:linear-gradient(90deg,#53c7fa,#38e4b0);border-radius:1.25rem 1.25rem 0 0;">
                <h5 class="modal-title text-white fw-bold" id="addGrowerModalLabel">Add New Grower</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.growers.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grower Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (optional)</label>
                        <input type="text" name="password" class="form-control" placeholder="Leave blank to auto-generate">
                        <small class="text-muted">If blank, a random password will be created and shown after submission.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Distributors</label>
                        <select name="distributors[]" class="form-select" multiple>
                            @foreach($distributors as $distributor)
                                <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl or ⌘ to select multiple.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius:0 0 1.25rem 1.25rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success px-4">Add Grower</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.grower-card {
    transition: box-shadow 0.18s;
}
.grower-card:hover {
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.12);
    transform: translateY(-2px) scale(1.02);
    z-index: 2;
}
</style>
@endsection
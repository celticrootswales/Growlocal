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
                    <span style="font-size:2.2rem;">👨‍🌾</span> Edit Grower
                </h1>
                <div class="fs-5 fw-normal" style="opacity:0.85;">Update grower details and connections</div>
            </div>
            <a href="{{ route('admin.growers.index') }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="border-radius: 2rem;">
                ← Back to Growers
            </a>
        </div>
    </div>

    {{-- Edit Grower Form --}}
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body p-4">
                    <h3 class="mb-4 fw-semibold" style="color: #1c657e;">Edit Grower Details</h3>
                    <form method="POST" action="{{ route('admin.growers.update', $grower->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $grower->email) }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Grower Name</label>
                            <input type="text" name="name" value="{{ old('name', $grower->name) }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $grower->business_name) }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (optional)</label>
                            <input type="text" name="password" class="form-control" placeholder="Leave blank to keep current password">
                            <small class="text-muted">Only fill this in if you want to change the password.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign Distributors</label>
                            <select name="distributors[]" class="form-select" multiple>
                                @foreach($distributors as $distributor)
                                    <option value="{{ $distributor->id }}" {{ $grower->distributors->contains($distributor->id) ? 'selected' : '' }}>
                                        {{ $distributor->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl or ⌘ to select multiple.</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success px-4 rounded-pill fw-bold shadow" type="submit">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                            <a href="{{ route('admin.growers.index') }}" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 1.25rem !important;
}
</style>
@endsection
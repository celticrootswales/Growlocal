
@extends('layouts.app')

@section('content')
<div class="container py-5">
        
    {{-- Header Banner --}}
    <div class="rounded-4 p-4 mb-4 d-flex flex-column flex-md-row align-items-center justify-content-between"
         style="background: linear-gradient(90deg, #53c7fa 0%, #38e4b0 100%); color: #fff;">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
                <span style="font-size:2.2rem;">🚚</span> Weekly Crop Plans
            </h1>
            <div class="fs-5 fw-normal" style="opacity:0.85;">All locked crop offerings for <b>{{ $year }}</b>. Click a card to arrange weekly plans for each offering.
    </p></div>
        </div>
        <div class="ms-md-4 mt-4 mt-md-0">
            <a href="{{ url()->current() }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="border-radius: 2rem;">
                Refresh List
            </a>
        </div>
    </div>

    @if($offerings->count())
        <div class="row g-4">
            @foreach($offerings as $offering)
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-3 h-100 shadow-lg text-start" style="border-radius:1.5rem;">
                        <div class="mb-2 d-flex align-items-center">
                            <div class="me-3" style="font-size:2.2rem;">
                                {{ $offering->icon ?? '🌱' }}
                            </div>
                            <div>
                                <div class="fw-bold" style="font-size:1.22rem;">
                                    {{ $offering->crop_name }} <span class="badge bg-light text-dark">{{ $offering->term }}</span>
                                </div>
                                <div class="text-muted small mb-1">Unit: {{ $offering->unit }} | Year: {{ $offering->year }}</div>
                                <div class="text-muted small">Distributors:
                                    @foreach($offering->distributors as $dist)
                                        <span class="badge bg-secondary">{{ $dist->name }}</span>
                                    @endforeach
                                </div>
                                <div class="text-muted small mt-1">
                                    Status: <span class="badge bg-danger">Locked</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.weekly-plans.plan', $offering->id) }}" class="btn btn-outline-primary rounded-pill mt-3 w-100">
                            Plan Weeks for {{ $offering->crop_name }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-4 text-center text-muted">No locked crop offerings found for {{ $year }}.</div>
    @endif
</div>
@endsection
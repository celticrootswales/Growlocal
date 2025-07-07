<?php use Carbon\Carbon; ?>

@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Header --}}
    <div class="rounded-4 p-4 mb-4" style="background: linear-gradient(90deg,#53c7fa 0%,#38e4b0 100%); color:#fff;">
        <div class="d-flex align-items-center">
            <span style="font-size:2.2rem;" class="me-3">🧮</span>
            <div>
                <h1 class="fw-bold mb-1" style="font-size:2.2rem;">My Weekly Estimates</h1>
                <div class="fs-5 fw-normal" style="opacity:0.85;">Submit your forecasted harvest quantities for upcoming deliveries.</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('grower.weekly-estimates.index', ['all' => $showAll ? null : 1]) }}"
           class="btn btn-sm {{ $showAll ? 'btn-outline-secondary' : 'btn-outline-success' }}">
            {{ $showAll ? 'Show Upcoming Only' : 'Show All Weeks' }}
        </a>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    @if ($allocations->isEmpty())
        <div class="alert alert-info">No weekly allocations assigned yet.</div>
    @else
        @php
            $grouped = $allocations->groupBy(function($allocation) {
                $date = $allocation->planned_date->format('Y-m-d');
                $distributor = $allocation->commitment->distributorNeed->distributor->name ?? 'Unknown';
                return "{$date}|{$distributor}";
            });
            $today = Carbon::now()->startOfWeek();
        @endphp

        @foreach ($grouped as $groupKey => $items)
            @php
                [$date, $distributor] = explode('|', $groupKey);
                $weekDate = Carbon::parse($date);
                $isPast = $weekDate->lt($today);
                $collapseId = 'collapse_' . md5($groupKey);
            @endphp

            <div class="card mb-4 shadow-sm rounded-3">
                <div class="card-header bg-light">
                    <button class="btn w-100 text-start fw-bold {{ $isPast ? 'collapsed' : '' }}"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="{{ $isPast ? 'false' : 'true' }}">
                        📅 Week of {{ $weekDate->format('j M Y') }} – {{ $distributor }}
                    </button>
                </div>

                <div class="collapse {{ $isPast ? '' : 'show' }}" id="{{ $collapseId }}">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Crop</th>
                                    <th>Planned Qty (kg)</th>
                                    <th>Your Estimate (kg)</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $allocation)
                                    @php
                                        $offering = $allocation->commitment->distributorNeed->cropOffering ?? null;
                                        $estimate = \App\Models\WeeklyEstimate::where('weekly_crop_plan_id', $allocation->weekly_crop_plan_id)
                                            ->where('grower_id', auth()->id())
                                            ->first();
                                    @endphp

                                    <tr>
                                        {{-- Save Form --}}
                                        <form method="POST" action="{{ route('grower.weekly-estimates.store') }}" class="d-flex align-items-center">
                                            @csrf
                                            <input type="hidden" name="weekly_allocation_id" value="{{ $allocation->id }}">
                                            <input type="hidden" name="weekly_crop_plan_id" value="{{ $allocation->weekly_crop_plan_id }}">

                                            <td>{{ $offering->crop_name ?? '—' }}</td>
                                            <td>{{ $allocation->quantity }}</td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="estimated_quantity"
                                                       value="{{ old('estimated_quantity', $estimate->estimated_quantity ?? '') }}"
                                                       class="form-control form-control-sm" style="width:100px;">
                                            </td>
                                            <td>
                                                <input type="text" name="notes"
                                                       value="{{ old('notes', $estimate->notes ?? '') }}"
                                                       class="form-control form-control-sm" style="width:200px;">
                                            </td>
                                            <td class="d-flex gap-1">
                                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                        </form>

                                        {{-- Delete Form (only if estimate exists) --}}
                                        @if($estimate)
                                            <form method="POST" action="{{ route('grower.weekly-estimates.destroy', $estimate->id) }}" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        @endif
                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
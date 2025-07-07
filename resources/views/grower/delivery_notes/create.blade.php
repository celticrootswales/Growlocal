@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Header Banner --}}
    <div class="rounded-4 p-4 mb-4" style="background: linear-gradient(90deg, #53c7fa 0%, #38e4b0 100%); color: #fff;">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
            <span style="font-size:2.2rem;">🚚</span> Create Delivery Note
        </h1>
        <div class="fs-5 fw-normal" style="opacity:0.85;">Log your weekly batch and print labels instantly</div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:1.1rem;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Delivery Note Form --}}
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4" style="margin:auto;">
        <form method="POST" action="{{ route('grower.delivery-notes.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Distributor --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Select Distributor <span class="text-danger">*</span></label>
                <select name="distributor_id" id="distributor-select" class="form-select rounded-pill" required>
                    <option value="">Choose distributor...</option>
                    @foreach($distributors as $distributor)
                        <option value="{{ $distributor->id }}" {{ old('distributor_id') == $distributor->id ? 'selected' : '' }}>
                            {{ $distributor->business_name ?? $distributor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Crops --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Crops Delivered <span class="text-danger">*</span></label>
                <div id="crops-list">
                    <div class="row g-2 mb-2 align-items-end crop-row">
                        <div class="col-7">
                            <select name="crops[0][crop_offering_id]" class="form-select rounded-pill" required>
                                <option value="">Select crop</option>
                                @foreach ($cropOfferings as $offering)
                                    @php
                                        $unit = $offering->unit ?? 'unit';
                                    @endphp
                                    <option value="{{ $offering->id }}">
                                        {{ $offering->crop_name }} ({{ $unit }})  £{{ number_format($offering->default_price ?? 0, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <input type="number" min="0.01" step="0.01" name="crops[0][quantity]" class="form-control rounded-pill"
                                placeholder="Qty" required>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-success btn-sm rounded-pill mt-2" id="add-crop">
                    + Add Another Crop
                </button>
            </div>

            {{-- Invoice Upload --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Attach Invoice (optional)</label>
                <input type="file" name="invoice" accept="image/*,application/pdf" class="form-control rounded-pill">
                <small class="text-muted">PDF, JPG, or PNG. Max 2MB.</small>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('grower.notes.index') }}" class="btn btn-secondary rounded-pill me-2">Cancel</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Create Delivery Note</button>
            </div>
        </form>
    </div>
</div>

{{-- Pass PHP data to JS --}}
<script>
    window.availableCrops = @json(
        $cropOfferings->map(function ($offering) {
            return [
                'id' => $offering->id,
                'label' => $offering->crop_name . ' (' . ($offering->unit_type ?? 'unit') . ')  £' . number_format($offering->default_price ?? 0, 2)
            ];
        })->values()
    );
</script>

{{-- Add/Remove Crop Row Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let cropIdx = 1;
    const cropsList = document.getElementById('crops-list');

    const distributorSelect = document.getElementById('distributor-select');
    distributorSelect.addEventListener('change', function () {
        const distributorId = this.value;
        if (!distributorId) return;

        fetch(`/grower/delivery-notes/offering-options/${distributorId}`)
            .then(res => res.json())
            .then(data => {
                window.availableCrops = data;
                refreshCropDropdowns();
            });
    });

    function refreshCropDropdowns() {
        const selects = document.querySelectorAll('select[name^="crops"][name$="[crop_offering_id]"]');
        selects.forEach(select => {
            const current = select.value;
            select.innerHTML = '<option value="">Select crop</option>';
            window.availableCrops.forEach(crop => {
                const selected = current == crop.id ? 'selected' : '';
                select.innerHTML += `<option value="${crop.id}" ${selected}>${crop.label}</option>`;
            });
        });
    }

    document.getElementById('add-crop').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-end crop-row';

        const options = (window.availableCrops || []).map(crop =>
            `<option value="${crop.id}">${crop.label}</option>`).join('');

        row.innerHTML = `
            <div class="col-7">
                <select name="crops[${cropIdx}][crop_offering_id]" class="form-select rounded-pill" required>
                    <option value="">Select crop</option>${options}
                </select>
            </div>
            <div class="col-4">
                <input type="number" min="0.01" step="0.01" name="crops[${cropIdx}][quantity]" class="form-control rounded-pill" placeholder="Qty" required>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-link text-danger px-2 remove-crop" title="Remove"><span style="font-size:1.4rem;">&minus;</span></button>
            </div>
        `;
        cropsList.appendChild(row);
        cropIdx++;
    });

    cropsList.addEventListener('click', e => {
        if (e.target.closest('.remove-crop')) {
            e.target.closest('.crop-row').remove();
        }
    });
});
</script>
@endsection
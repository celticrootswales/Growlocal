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
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
            🗓️ Weekly Planning: {{ $offering->crop_name }} ({{ $offering->unit }})
        </h1>
        <div class="fs-5" style="opacity:0.85;">
            Term: <strong>{{ $offering->term }}</strong> | Year: {{ $offering->year }}<br>
            Distributors:
            @foreach($offering->distributors as $distributor)
                <span class="badge rounded-pill bg-light text-dark border me-1">{{ $distributor->name }}</span>
            @endforeach
        </div>
    </div>

    {{-- ✅ MAIN FORM FOR SAVING --}}
    <form action="{{ route('admin.weekly-plans.save.batch') }}" method="POST">
        @csrf

        @foreach ($offering->growerCommitments as $commitment)
            <div class="card mb-4 shadow-sm rounded-4">
                <div class="card-header bg-white rounded-top-4 d-flex justify-content-between align-items-center">
                    <span class="fw-bold">{{ $commitment->grower->name }}</span>
                    <span class="badge bg-info fs-5 text-light">Commitment {{ $commitment->committed_quantity }} {{ $offering->unit }}</span>
                </div>
                <div class="card-body">
                    <table class="table align-middle" id="table-{{ $commitment->id }}">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Quantity ({{ $offering->unit }})</th>
                                <th style="width:90px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commitment->weeklyAllocations as $index => $alloc)
                                <tr>
                                    <td>
                                        <input type="hidden" name="weekly[{{ $commitment->id }}][{{ $index }}][allocation_id]" value="{{ $alloc->id }}">
                                        <input type="date" name="weekly[{{ $commitment->id }}][{{ $index }}][date]" value="{{ $alloc->planned_date->format('Y-m-d') }}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" name="weekly[{{ $commitment->id }}][{{ $index }}][quantity]" value="{{ $alloc->quantity }}" step="0.01" min="0" class="form-control">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="submitDelete({{ $alloc->id }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addRow({{ $commitment->id }})">
                        ➕ Add Week
                    </button>
                    <div class="mt-2 text-end">
                        <strong>Total Allocated:</strong>
                        <span id="total-{{ $commitment->id }}">0</span> {{ $offering->unit }}
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-end mt-4">
            <button class="btn btn-success btn-lg px-4 rounded-pill shadow-sm fw-bold">
                Save All Plans
            </button>
        </div>
    </form>

    {{-- ✅ DELETE FORMS (kept outside main form to avoid method conflict) --}}
    @foreach($offering->growerCommitments as $commitment)
        @foreach($commitment->weeklyAllocations as $alloc)
            <form id="delete-form-{{ $alloc->id }}" action="{{ route('admin.weekly-plans.allocation.delete', $alloc->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endforeach
</div>

<script>
function addRow(commitmentId) {
    const tbody = document.querySelector(`#table-${commitmentId} tbody`);
    const index = tbody.querySelectorAll('tr').length;

    const dateInputs = tbody.querySelectorAll(`input[name^="weekly[${commitmentId}]"][type="date"]`);
    let nextDate = '';
    if (dateInputs.length > 0) {
        const lastDateValue = dateInputs[dateInputs.length - 1].value;
        if (lastDateValue) {
            const lastDate = new Date(lastDateValue);
            lastDate.setDate(lastDate.getDate() + 7);
            nextDate = lastDate.toISOString().split('T')[0];
        }
    }

    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <input type="date" name="weekly[${commitmentId}][${index}][date]" class="form-control" value="${nextDate}" onchange="updateTotal(${commitmentId})">
        </td>
        <td>
            <input type="number" name="weekly[${commitmentId}][${index}][quantity]" class="form-control" step="0.01" min="0" oninput="updateTotal(${commitmentId})">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); updateTotal(${commitmentId})">
                <i class="bi bi-x"></i>
            </button>
        </td>`;
    tbody.appendChild(row);
    updateTotal(commitmentId);
}

function updateTotal(commitmentId) {
    const inputs = document.querySelectorAll(`#table-${commitmentId} input[name^="weekly[${commitmentId}]"][type="number"]`);
    let total = 0;
    inputs.forEach(input => {
        const val = parseFloat(input.value);
        if (!isNaN(val)) total += val;
    });
    document.getElementById(`total-${commitmentId}`).innerText = total.toFixed(2);
}

function submitDelete(allocationId) {
    if (confirm('Are you sure you want to delete this row?')) {
        document.getElementById(`delete-form-${allocationId}`).submit();
    }
}

window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table[id^="table-"]').forEach(table => {
        const id = table.id.split('-')[1];
        updateTotal(id);
    });
});
</script>
@endsection
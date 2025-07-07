@extends('layouts.app')
@section('content')
<div class="container py-5">

    {{-- Header Banner --}}
    <div class="rounded-4 p-4 mb-4 d-flex flex-column flex-md-row align-items-center justify-content-between"
         style="background: linear-gradient(90deg, #53c7fa 0%, #38e4b0 100%); color: #fff;">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 2.2rem;">
                <span style="font-size:2.2rem;">🚚</span> Delivery Notes
            </h1>
            <div class="fs-5 fw-normal" style="opacity:0.85;">Every delivery, at a glance—traceability, recalls, and more</div>
        </div>
        <div class="ms-md-4 mt-4 mt-md-0">
            <a href="{{ url()->current() }}" class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="border-radius: 2rem;">
                Refresh List
            </a>
        </div>
    </div>

    {{-- Search and Filter --}}
    <form method="GET" action="{{ route('admin.notes') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-4">
            <label class="form-label mb-0 fw-semibold">Search Trace #</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Type a traceability number...">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.notes') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    @if($notes->isEmpty())
        <div class="alert alert-info">No delivery notes found.</div>
    @else
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:linear-gradient(90deg,#f8fafc 60%,#eafcf9 100%);">
                            <tr>
                                <th class="text-nowrap">Date</th>
                                <th>Grower</th>
                                <th>Distributor</th>
                                <th>Trace #</th>
                                <th>Status</th>
                                <th>Crops</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notes as $note)
                                <tr>
                                    <td class="text-nowrap">{{ $note->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $note->user->business_name ?? $note->user->name ?? 'Unknown' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $note->distributor->name ?? '—' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-monospace text-primary">{{ $note->traceability_number }}</span>
                                    </td>
                                    <td>
                                        @if($note->recalled)
                                            <span class="badge bg-danger px-3 py-2">Recalled</span>
                                        @else
                                            <span class="badge bg-success px-3 py-2">{{ ucfirst($note->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="mb-0 ps-3 small">
                                            @foreach($note->boxes as $box)
                                                <li>{{ $box->crop }} ({{ $box->quantity }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        @if($note->recalled)
                                            <form action="{{ route('admin.recall.remove', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove recall for this batch?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill">Remove Recall</button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-warning rounded-pill" data-bs-toggle="modal" data-bs-target="#recallModal" data-id="{{ $note->id }}">
                                                Recall
                                            </button>
                                        @endif
                                        <a href="{{ route('delivery-notes.pdf', $note->id) }}" class="btn btn-sm btn-outline-info rounded-pill ms-1">PDF</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal -->
    <div class="modal fade" id="recallModal" tabindex="-1" aria-labelledby="recallModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.recall', 0) }}" id="recallForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(90deg,#53c7fa,#38e4b0);border-radius:1.25rem 1.25rem 0 0;">
                    <h5 class="modal-title text-white fw-bold" id="recallModalLabel">Recall Delivery Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <input type="hidden" name="note_id" id="noteIdInput">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Recall</label>
                        <select name="reason" class="form-select" required>
                            <option value="">Select a reason</option>
                            <option value="Contaminated">Contaminated</option>
                            <option value="Moldy">Moldy</option>
                            <option value="Incorrect labeling">Incorrect labeling</option>
                            <option value="Spoiled during transit">Spoiled during transit</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius:0 0 1.25rem 1.25rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Confirm Recall</button>
                </div>
            </div>
        </form>
      </div>
    </div>

</div>

<script>
    const recallModal = document.getElementById('recallModal');
    recallModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const noteId = button.getAttribute('data-id');
        const form = document.getElementById('recallForm');
        form.action = `/admin/recall/${noteId}`;
    });
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4 fw-bold text-primary">📄 All Delivery Notes</h1>

    <!-- Search form -->
    <form method="GET" action="{{ route('admin.notes') }}" class="row g-3 align-items-center mb-4">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Trace #">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.notes') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    @if($notes->isEmpty())
        <div class="alert alert-info">No delivery notes found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Grower</th>
                        <th>Distributor</th>
                        <th>Trace #</th>
                        <th>Status</th>
                        <th>Crops</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notes as $note)
                    <tr>
                        <td>{{ $note->created_at->format('Y-m-d') }}</td>
                        <td>{{ $note->user->name ?? 'Unknown' }}</td>
                        <td>{{ $note->distributor->name ?? '—' }}</td>
                        <td>{{ $note->traceability_number }}</td>
                        <td>
                            @if($note->recalled)
                                <span class="badge bg-danger">Recalled</span>
                            @else
                                <span class="badge bg-success">{{ $note->status }}</span>
                            @endif
                        </td>
                        <td>
                            <ul class="mb-0">
                                @foreach($note->boxes as $box)
                                    <li>{{ $box->crop }} ({{ $box->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if($note->recalled)
                                <!-- Remove Recall -->
                                <form action="{{ route('admin.recall.remove', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove recall for this batch?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove Recall</button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#recallModal" data-id="{{ $note->id }}">
                                    Recall
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="recallModal" tabindex="-1" aria-labelledby="recallModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.recall', 0) }}" id="recallForm">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recallModalLabel">Recall Delivery Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Confirm Recall</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- JS to update form action -->
<script>
    const recallModal = document.getElementById('recallModal');
    recallModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const noteId = button.getAttribute('data-id');
        const form = document.getElementById('recallForm');

        // Update action URL dynamically
        form.action = `/admin/recall/${noteId}`;
    });
</script>
@endsection
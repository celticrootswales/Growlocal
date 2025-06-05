@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 fw-bold">📦 Your Delivery Notes</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($notes->isEmpty())
        <div class="alert alert-info">You haven't submitted any delivery notes yet.</div>
    @else
        <div class="table-responsive bg-white shadow-sm rounded p-3">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Distributor</th>
                        <th>Trace #</th>
                        <th>Crops</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notes as $note)
                        <tr>
                            <td>{{ $note->created_at->format('Y-m-d') }}</td>
                            <td>{{ $note->distributor->name ?? '—' }}</td>
                            <td>{{ $note->traceability_number }}</td>
                            <td>
                                <ul class="mb-0 list-unstyled">
                                    @foreach($note->boxes as $box)
                                        <li>{{ $box->crop }} ({{ $box->quantity }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                @if($note->recalled)
                                    <span class="badge bg-danger">⚠ Recalled</span>
                                @elseif($note->status === 'Delivered')
                                    <span class="badge bg-success">✔ Delivered</span>
                                @else
                                    <span class="badge bg-warning text-dark">⏳ Pending</span>
                                @endif
                            </td>
                            <td class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('grower.delivery-notes.pdf', $note->id) }}" class="btn btn-sm btn-outline-secondary">📄 PDF</a>
                                <a href="{{ route('grower.delivery-notes.label', $note->id) }}" class="btn btn-sm btn-outline-dark">🏷 Label</a>

                                <form action="{{ route('grower.delivery-notes.markDelivered', $note->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" {{ $note->status === 'Delivered' ? 'disabled' : '' }}>
                                        Mark Delivered
                                    </button>
                                </form>

                                <form action="{{ route('grower.delivery-notes.delete', $note->id) }}" method="POST" onsubmit="return confirm('Delete this delivery note?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
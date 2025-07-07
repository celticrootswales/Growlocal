@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- Header Banner with Back/Create button --}}
    <div class="rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between"
         style="background: linear-gradient(90deg,#53c7fa 0%,#38e4b0 100%);color:#fff;">
        <div>
            <h1 class="fw-bold mb-1" style="font-size:2.2rem;">
                <span style="font-size:2.2rem;">📦</span> My Delivery Notes
            </h1>
            <div class="fs-5 fw-normal" style="opacity:0.85;">All your submitted and pending batches</div>
        </div>
        <a href="{{ route('grower.delivery-notes.create') }}"
           class="btn btn-light px-4 py-2 fw-bold shadow-sm"
           style="border-radius: 2rem;">
            + Create Delivery Note
        </a>
    </div>

    {{-- Success/Error --}}
    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
    @endif

    {{-- Table or Empty State --}}
    @if($notes->isEmpty())
        <div class="alert alert-info mt-4 rounded-4 shadow-sm">
            <span style="font-size:1.5rem;">🤷‍♂️</span> No delivery notes yet.<br>
            <a href="{{ route('grower.delivery-notes.create') }}" class="btn btn-primary btn-sm mt-3 rounded-pill">Create your first Delivery Note</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle rounded-4 overflow-hidden shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
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
                        <td>{{ $note->created_at->format('d M Y') }}</td>
                        <td>
                            {{ $note->distributor->business_name ?? $note->distributor->name ?? '—' }}
                        </td>
                        <td>
                            <span class="fw-bold">{{ $note->traceability_number }}</span>
                        </td>
                        <td>
                            @if($note->recalled)
                                <span class="badge bg-danger rounded-pill px-3">Recalled</span>
                            @else
                                <span class="badge bg-success rounded-pill px-3">{{ ucfirst($note->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <ul class="mb-0 ps-3 small">
                                @foreach($note->boxes as $box)
                                    <li>{{ $box->crop }} <span class="text-muted">({{ $box->quantity }})</span></li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <a href="{{ route('grower.delivery-notes.pdf.single', $note->id) }}"
                               class="btn btn-outline-info btn-sm rounded-pill mb-1" target="_blank" title="Download PDF">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                            <a href="{{ route('grower.delivery-notes.label', $note->id) }}"
                               class="btn btn-outline-secondary btn-sm rounded-pill mb-1" target="_blank" title="Print Label">
                                <i class="bi bi-tag"></i> Label
                            </a>
                            <form action="{{ route('grower.delivery-notes.delete', $note->id) }}"
                                  method="POST" class="d-inline" onsubmit="return confirm('Delete this note?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill mb-1">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @if(!$note->recalled && $note->status !== 'Delivered')
                                <form action="{{ route('grower.delivery-notes.markDelivered', $note->id) }}"
                                      method="POST" class="d-inline ms-1">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill mb-1">
                                        <i class="bi bi-check2-circle"></i> Mark Delivered
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
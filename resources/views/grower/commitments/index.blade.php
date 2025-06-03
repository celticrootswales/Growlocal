@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-success mb-4">🧑‍🌾 My Crop Commitments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-success">
            <tr>
                <th>Crop</th>
                <th>Unit</th>
                <th>Distributor Need</th>
                <th>Your Commitment</th>
                <th>Notes</th>
                <th>Save</th>
            </tr>
        </thead>
        <tbody>
            @foreach($needs as $need)
                <tr>
                    <form method="POST" action="{{ route('grower.commitments.store') }}">
                        @csrf
                        <input type="hidden" name="distributor_crop_need_id" value="{{ $need->id }}">
                        <td>{{ $need->cropOffering->emoji }} {{ $need->cropOffering->name }}</td>
                        <td>{{ $need->cropOffering->unit }}</td>
                        <td>{{ $need->desired_quantity }}</td>
                        <td>
                            <input type="number" name="committed_quantity" value="{{ $commitments[$need->id]->committed_quantity ?? '' }}" class="form-control" min="0">
                        </td>
                        <td>
                            <input type="text" name="notes" value="{{ $commitments[$need->id]->notes ?? '' }}" class="form-control">
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-success">💾</button>
                        </td>
                    </form>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
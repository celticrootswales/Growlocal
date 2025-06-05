@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Edit Commitment</h2>

    <form method="POST" action="{{ route('grower.commitments.update', $commitment->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Committed Quantity</label>
            <input type="number" name="committed_quantity" class="form-control" value="{{ old('committed_quantity', $commitment->committed_quantity) }}" required>
        </div>

        <div class="mb-3">
            <label>Notes (optional)</label>
            <textarea name="notes" class="form-control">{{ old('notes', $commitment->notes) }}</textarea>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('grower.commitments.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
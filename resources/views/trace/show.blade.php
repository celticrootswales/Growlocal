@extends('layouts.app')

@section('content')
    @if($note->recalled)
        <div class="alert alert-danger">
            ⚠️ <strong>This batch has been recalled.</strong> Please do not consume or distribute this produce until further notice.
        </div>
    @endif
<div class="max-w-xl mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-xl font-bold mb-4">Trace Results</h1>

    <p><strong>Crop:</strong> {{ $box->crop }}</p>
    <p><strong>Quantity:</strong> {{ $box->quantity }}</p>
    <p><strong>Label Code:</strong> {{ $box->label_code }}</p>

    <hr class="my-4">

    <p><strong>Destination:</strong> {{ $note->destination }}</p>
    <p><strong>Date Harvested:</strong> {{ $note->created_at->format('Y-m-d') }}</p>

    @if($grower)
        <hr class="my-4">
        <p><strong>Grown By:</strong> {{ $grower->name }}</p>
        <p><strong>Farm Name:</strong> {{ $grower->business_name }}</p>
        <p><strong>Location:</strong> {{ $grower->location }}</p>
    @endif

    {{-- Optional: Add image or GPS if attached --}}
</div>
@endsection
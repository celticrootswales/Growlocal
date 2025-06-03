@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="fw-bold text-success">Crop Needs</h1>

    @if($needs->count())
        <ul class="list-group">
            @foreach($needs as $need)
                <li class="list-group-item">
                    {{ $need->cropOffering->name }} — {{ $need->quantity }} {{ $need->cropOffering->unit }}
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-muted">No crop needs defined yet.</p>
    @endif
</div>
@endsection
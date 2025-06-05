@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1>Edit Grower</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.growers.update', $grower->id) }}" method="POST" class="mb-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $grower->email }}" required>
        </div>
        <div class="mb-3">
            <label>Grower Name</label>
            <input type="text" name="name" class="form-control" value="{{ $grower->name }}" required>
        </div>
        <div class="mb-3">
            <label>Business Name</label>
            <input type="text" name="business_name" class="form-control" value="{{ $grower->business_name }}" required>
        </div>

        <div class="mb-3">
            <label>Distributors</label>
            <select name="distributors[]" class="form-control" multiple>
                @foreach($distributors as $dist)
                    <option value="{{ $dist->id }}" @if($grower->distributors->contains($dist->id)) selected @endif>
                        {{ $dist->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">Update Grower</button>
        <a href="{{ route('admin.growers.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1>Manage Growers</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.growers.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="mb-2">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Grower Name</label>
            <input type="text" name="name" class="form-control">
        </div>
        <div class="mb-2">
            <label>Business Name</label>
            <input type="text" name="business_name" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Assign Distributors</label>
            <select name="distributors[]" class="form-control" multiple>
                @foreach(\App\Models\User::role('distributor')->get() as $distributor)
                    <option value="{{ $distributor->id }}">{{ $distributor->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-success">Add Grower</button>
    </form>

    <div class="row">
        @forelse($growers as $grower)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>{{ $grower->name }}</h5>
                        <p>{{ $grower->business_name }}</p>
                        <p>{{ $grower->email }}</p>
                        <p>
                            <strong>Distributors:</strong>
                            {{ $grower->distributors->pluck('name')->join(', ') ?: 'None' }}
                        </p>
                        <a href="{{ route('admin.growers.edit', $grower->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('admin.growers.destroy', $grower->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this grower?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>No growers found.</p>
        @endforelse
    </div>
</div>
@endsection
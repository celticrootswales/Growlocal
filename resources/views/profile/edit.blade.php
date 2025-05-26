@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-primary mb-4">Edit Profile</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="card shadow-sm p-4 mb-5">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name', auth()->user()->name) }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email', auth()->user()->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="business_name" class="form-label">Farm Name / Business Name</label>
            <input id="business_name" type="text" class="form-control" name="business_name" value="{{ old('business_name', auth()->user()->business_name) }}">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input id="location" type="text" class="form-control" name="location" value="{{ old('location', auth()->user()->location) }}">
        </div>

        <button type="submit" class="btn btn-success mt-3">💾 Save Profile</button>
    </form>

    <!-- Password Update Section -->
    <form method="POST" action="{{ route('password.update') }}" class="card shadow-sm p-4 mb-5">
        @csrf
        @method('PUT')

        <h5 class="mb-3">🔐 Change Password</h5>

        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input id="current_password" type="password" class="form-control" name="current_password" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input id="password" type="password" class="form-control" name="password" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">🔄 Update Password</button>
    </form>

    <!-- Delete Account -->
    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account?');" class="text-end">
        @csrf
        @method('DELETE')

        <button class="btn btn-outline-danger">🗑 Delete Account</button>
    </form>
</div>
@endsection
@extends('layouts.guest')

@section('content')
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center px-0" style="background: #f7fafc;">
    <div class="row w-100 g-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 900px;">
        <!-- Left: Login Form -->
        <div class="col-md-6 bg-white p-5 d-flex flex-column justify-content-center">
            <div class="mb-4 text-center">
                <img src="{{ asset('img/wvis-logo.jpg') }}" alt="GrowLocal Logo" style="max-width: 140px;" class="mb-3">
                <h1 class="fw-bold mb-1 text-success" style="font-size:2rem;">Sign In to GrowLocal</h1>
                <div class="text-muted mb-3">Access your dashboard and manage your crops</div>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-success btn-lg fw-bold">Sign In</button>
                </div>
                @if (Route::has('password.request'))
                    <div class="text-center">
                        <a class="text-decoration-underline small text-muted" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    </div>
                @endif
            </form>
        </div>
        <!-- Right: Image/Branding -->
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center bg-success-subtle position-relative" style="background: linear-gradient(120deg,#53c7fa 0,#38e4b0 100%);">
            <img src="{{ asset('img/l') }}" alt="Fresh Local Produce" class="img-fluid rounded-0 shadow" style="max-height:440px;">
            <!-- Optionally add a branding message or overlay here -->
            <div class="position-absolute bottom-0 start-0 p-4 text-white" style="background:rgba(0,0,0,0.2);width:100%;">
                <div class="fw-bold" style="font-size:1.2rem;">Powered by Welsh growers, for Welsh schools.</div>
            </div>
        </div>
    </div>
</div>
@endsection
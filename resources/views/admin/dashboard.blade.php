@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h1 class="fw-bold">Admin Dashboard</h1>
    <p class="text-muted">Welcome, Admin. Monitor delivery status and food safety.</p>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <h5 class="card-title">🔍 Review Delivery Notes</h5>
                    <p class="card-text">Audit grower submissions for traceability.</p>
                    <a href="{{ route('admin.notes') }}" class="btn btn-outline-info">View Notes</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title">⚠ Recall Alerts</h5>
                    <p class="card-text">View and manage flagged batches.</p>
                    <a href="{{ route('admin.recalls') }}" class="btn btn-outline-danger">Manage Recalls</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
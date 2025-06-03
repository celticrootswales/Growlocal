@auth
    @role('admin')
        <li class="nav-item rounded-0 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('admin.dashboard') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.notes') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('admin.notes') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-boxes me-2"></i> Delivery Notes
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.recalls') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('admin.recalls') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-exclamation-triangle me-2"></i> Recalls
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.crop-offerings.index') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('admin.crop-offerings.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-flower1 me-2"></i> Crop Offerings
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('admin.weekly-plans.index') }}"
               class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('admin.weekly-plans.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                📅 Weekly Plans
            </a>
        </li>
    @endrole

    @role('grower')
        <li class="nav-item mb-2">
            <a href="{{ route('grower.dashboard') }}" class="btn w-100 border-start-0 border-end-0 text-start rounded-0 py-3 fs-6 {{ Route::is('grower.dashboard') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('grower.notes.index') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('grower.notes.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-box-seam me-2"></i> My Deliveries
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('grower.delivery-notes.create') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('grower.delivery-notes.create') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-plus-square me-2"></i> Create Delivery
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('grower.crop-plan.index') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('grower.crop-plan.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-journal-text me-2"></i> Crop Plan
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('grower.commitments.index') }}"
               class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('grower.commitments.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                🧑‍🌾 Yearly Commitments
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('grower.weekly-estimates.index') }}"
               class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('grower.weekly-estimates.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                📅 My Weekly Estimates
            </a>
        </li>
    @endrole

    @role('distributor')
        <li class="nav-item mb-2">
            <a href="{{ route('distributor.dashboard') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('distributor.dashboard') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('distributor.crop-plan.index') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('distributor.crop-plan.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-leaf me-2"></i> Crop Plan
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('distributor.recalls') }}" class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('distributor.recalls') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                <i class="bi bi-exclamation-octagon me-2"></i> Recalls
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('distributor.crop-needs.index') }}"
               class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('distributor.crop-needs.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                🧺 Crop Needs
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('distributor.weekly-overview.index') }}"
               class="btn w-100 text-start rounded-0 border-start-0 border-end-0 py-3 fs-6 {{ Route::is('distributor.weekly-overview.index') ? 'btn-light text-success fw-bold' : 'btn-outline-light text-white' }}">
                📅 Weekly Estimates
            </a>
        </li>
    @endrole
@endauth
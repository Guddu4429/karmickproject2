<div>
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Profile & Settings</h4>
        <small>View profile and manage security settings</small>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Personal Information</h5>
        <div class="row g-4">
            <div class="col-md-4">
                <small class="text-muted">Name</small>
                <p class="fw-semibold mb-0">{{ $user->name ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Email</small>
                <p class="fw-semibold mb-0">{{ $user->email ?? '-' }}</p>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Phone</small>
                <p class="fw-semibold mb-0">{{ $user->phone ?? $teacher->phone ?? '-' }}</p>
            </div>
            @if($teacher)
                <div class="col-md-4">
                    <small class="text-muted">Employee Code</small>
                    <p class="fw-semibold mb-0">{{ $teacher->employee_code ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Designation</small>
                    <p class="fw-semibold mb-0">{{ $teacher->designation ?? '-' }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-semibold mb-3">Change Password</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control" wire:model.defer="current_password" placeholder="Current password">
                @error('current_password')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" wire:model.defer="new_password" placeholder="New password">
                @error('new_password')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" wire:model.defer="new_password_confirmation" placeholder="Confirm password">
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary btn-sm" wire:click="changePassword" wire:loading.attr="disabled">
                Change Password
            </button>
        </div>
    </div>
</div>

<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Settings</h4>
        <small>
            @if(!empty($student))
                {{ $student->first_name }} {{ $student->last_name }} • Class {{ $student->class_name ?? '-' }} {{ $student->stream_name ? '('.$student->stream_name.')' : '' }}
            @else
                Manage your account and contact details
            @endif
        </small>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (empty($student))
        <div class="alert alert-info">
            Please select a student first.
        </div>
    @else
        <!-- Personal Contact Details (Student) -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Student Contact Details</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Student Name</label>
                    <input type="text" class="form-control" value="{{ $student->first_name }} {{ $student->last_name }}" disabled>
                    <small class="text-muted">Name cannot be changed</small>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" rows="2" wire:model.defer="studentAddress">{{ $studentAddress }}</textarea>
                </div>
            </div>
        </div>

        <!-- Guardian Contact Details -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Guardian Contact Details</h5>

            @if(!empty($guardian))
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Guardian Name</label>
                        <input type="text" class="form-control" wire:model.defer="guardianName">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" wire:model.defer="guardianPhone">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Email ID</label>
                        <input type="email" class="form-control" wire:model.defer="guardianEmail">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label class="form-label">Guardian Address</label>
                        <textarea class="form-control" rows="2" wire:model.defer="guardianAddress"></textarea>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-primary btn-sm" wire:click="save">
                        Save Changes
                    </button>
                </div>
            @else
                <p class="text-muted mb-0">No guardian details found.</p>
            @endif
        </div>

        <!-- Change Password (for guardian account) -->
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-semibold mb-3">Change Password (Guardian Account)</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-control" placeholder="Current password" wire:model.defer="current_password">
                    @error('current_password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" placeholder="New password" wire:model.defer="new_password">
                    @error('new_password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" placeholder="Confirm new password" wire:model.defer="new_password_confirmation">
                </div>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-danger btn-sm"
                        wire:click="changePassword"
                        wire:loading.attr="disabled"
                        wire:target="changePassword">
                    Change Password
                </button>
            </div>
        </div>
    @endif
</div>

<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Settings</h4>
        <small>Manage your account and contact details</small>
    </div>

    <!-- Profile Picture -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Profile Picture</h5>

        <div class="d-flex align-items-center gap-4 flex-wrap">
            <!-- Current Photo -->
            <div class="text-center">
                <img src="{{ asset('images/profile-placeholder.jpg') }}"
                     alt="Profile Picture"
                     class="rounded-circle border"
                     width="120"
                     height="120"
                     style="object-fit: cover;">
            </div>

            <!-- Upload -->
            <div>
                <label class="form-label">Change Profile Picture</label>
                <input type="file"
                       class="form-control"
                       accept="image/png, image/jpeg">
                <small class="text-muted">
                    Allowed formats: JPG, PNG (Max 2MB)
                </small>

                <div class="mt-3">
                    <button class="btn btn-primary btn-sm">
                        Update Profile Picture
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Contact Details -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Personal Contact Details</h5>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Student Name</label>
                <input type="text" class="form-control" value="{{ Auth::user()->name ?? 'Student Name' }}" disabled>
                <small class="text-muted">Name cannot be changed</small>
            </div>

            <div class="col-md-4">
                <label class="form-label">Email ID</label>
                <input type="email" class="form-control" value="{{ Auth::user()->email ?? 'student@dpsrpk.edu' }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-control" value="+91 98765 43210">
            </div>

            <div class="col-md-12">
                <label class="form-label">Address</label>
                <textarea class="form-control" rows="2">Kolkata, West Bengal</textarea>
            </div>
        </div>

        <div class="text-end mt-3">
            <button class="btn btn-primary btn-sm">
                Save Changes
            </button>
        </div>
    </div>

    <!-- Guardian Contact Details -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-semibold mb-0">Guardian Contact Details</h5>
            <button class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>
                Add Guardian
            </button>
        </div>

        <!-- Guardian 1 -->
        <div class="border rounded-3 p-3 mb-3">
            <h6 class="fw-semibold mb-3">Guardian 1</h6>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Guardian Name</label>
                    <input type="text" class="form-control" value="Mr. ABC Singh" disabled>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Relation</label>
                    <input type="text" class="form-control" value="Father">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" value="+91 91234 56789">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Email ID</label>
                    <input type="email" class="form-control" value="guardian@email.com">
                </div>
            </div>
        </div>

        <!-- Guardian 2 -->
        <div class="border rounded-3 p-3 mb-3">
            <h6 class="fw-semibold mb-3">Guardian 2</h6>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Guardian Name</label>
                    <input type="text" class="form-control" value="Mrs. XYZ Singh">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Relation</label>
                    <input type="text" class="form-control" value="Mother">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" value="+91 99887 66554">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Email ID</label>
                    <input type="email" class="form-control" value="mother@email.com">
                </div>
            </div>
        </div>

        <div class="text-end mt-3">
            <button class="btn btn-primary btn-sm">
                Update Guardian Details
            </button>
        </div>
    </div>

    <!-- Change Password -->
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-semibold mb-3">Change Password</h5>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control">
            </div>
        </div>

        <div class="text-end mt-3">
            <button class="btn btn-danger btn-sm">
                Change Password
            </button>
        </div>
    </div>
</div>

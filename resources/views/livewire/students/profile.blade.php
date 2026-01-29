<div>
    <!-- Main Content -->
        <!-- Header -->
        <div class="bg-primary text-white rounded-4 p-4 mb-4">
            <h4 class="mb-1">Student Profile</h4>
            <small>View your personal and academic information</small>
        </div>

        <!-- Personal Information -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Personal Information</h5>

            <div class="row g-4 align-items-center">
                <div class="col-md-3 text-center">
                    <img src="{{ asset('images/profile-placeholder.jpg') }}"
                         class="rounded-circle img-fluid mb-2"
                         alt="Student Photo"
                         style="width: 120px; height: 120px; object-fit: cover;">
                    <p class="fw-semibold mb-0">{{ Auth::user()->name ?? 'Student Name' }}</p>
                    <small class="text-muted">Roll No: 23A014</small>
                </div>

                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted">Class & Section</small>
                            <p class="fw-semibold mb-0">X - A</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Date of Birth</small>
                            <p class="fw-semibold mb-0">12 Jan 2009</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Phone Number</small>
                            <p class="fw-semibold mb-0">+91 98765 43210</p>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted">Email ID</small>
                            <p class="fw-semibold mb-0">{{ Auth::user()->email ?? 'student@dpsrpk.edu' }}</p>
                        </div>

                        <div class="col-md-8">
                            <small class="text-muted">Address</small>
                            <p class="fw-semibold mb-0">
                                Kolkata, West Bengal, India
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guardian Information -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Guardian Information</h5>

            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted">Guardian Name</small>
                    <p class="fw-semibold mb-0">Mr. ABC Singh</p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Relation</small>
                    <p class="fw-semibold mb-0">Father</p>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Contact Number</small>
                    <p class="fw-semibold mb-0">+91 91234 56789</p>
                </div>
            </div>
        </div>

        <!-- Previous Academic Records -->
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-semibold mb-3">Previous Academic Records</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Board</th>
                            <th>School Name</th>
                            <th>Year</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CBSE</td>
                            <td>ABC Public School</td>
                            <td>2024</td>
                            <td>450 / 500</td>
                            <td>90%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-outline-primary btn-sm">
                    View Previous Marksheet
                </button>
            </div>
        </div>
</div>

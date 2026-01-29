<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Welcome, {{ Auth::user()->name ?? 'Student Name' }}</h4>
        <small>{{ now()->format('l, F d, Y') }}</small>
    </div>

        <!-- KPI Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <a href="{{ route('student.attendance') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 rounded-4 p-3 hover-shadow">
                        <small class="text-muted">Attendance</small>
                        <h3 class="fw-bold">90%</h3>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <small class="text-muted">Fees Due</small>
                <h3 class="fw-bold">₹5,000</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <small class="text-muted">Upcoming Exam</small>
                <h6 class="fw-semibold mb-0">Unit Test</h6>
                <small class="text-muted">Feb 10, 2026</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <small class="text-muted">Overall Grade</small>
                <h3 class="fw-bold text-success">A</h3>
            </div>
        </div>
    </div>

    <!-- Academic Performance -->
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">Academic Performance</h5>

                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Unit Test</th>
                                <th>Half-Yearly</th>
                                <th>Annual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Math</td>
                                <td>85</td>
                                <td>92</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>Science</td>
                                <td>78</td>
                                <td>88</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>English</td>
                                <td>90</td>
                                <td>85</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-outline-primary btn-sm mt-2">
                    Download Marksheet
                </button>
            </div>
        </div>

        <!-- Alerts -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">Alerts & Notifications</h5>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">💳 Fee payment due by Jan 31</li>
                    <li class="list-group-item px-0">📘 Science marks updated</li>
                    <li class="list-group-item px-0">📅 Parent-Teacher Meeting</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-2">Upcoming Activities</h5>
                <p class="mb-1">🔬 Science Project – Feb 5</p>
                <p class="mb-0">👨‍👩‍👧 Parent-Teacher Meeting</p>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <h5 class="fw-semibold mb-2">Upcoming Activities</h5>
                    <p class="mb-1">🔬 Science Project – Feb 5</p>
                    <p class="mb-0">👨‍👩‍👧 Parent-Teacher Meeting</p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold mb-0">Attendance Trend</h5>
                        <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-outline-primary">
                            View Details <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div style="height: 220px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance Chart
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Attendance %',
                    data: [85, 88, 90, 87, 92, 90],
                    borderColor: 'rgb(13, 110, 253)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 100
                    }
                }
            }
        });
    }
</script>
<script src="{{ asset('js/attendance-chart.js') }}"></script>
@endpush

<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Attendance</h4>
        <small>View your attendance records and status</small>
    </div>

    <!-- Attendance Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <small class="text-muted">Overall Attendance</small>
                <h2 class="fw-bold mb-0">90%</h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <small class="text-muted">Attendance Cutoff</small>
                <h2 class="fw-bold mb-0">75%</h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <small class="text-muted">Status</small>
                <h5 class="fw-semibold text-success mb-0">
                    Eligible
                </h5>
            </div>
        </div>
    </div>

    <!-- Subject-wise Attendance -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Subject-wise Attendance</h5>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Total Classes</th>
                        <th>Attended</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mathematics</td>
                        <td>45</td>
                        <td>42</td>
                        <td>93%</td>
                        <td><span class="badge bg-success">Above Cutoff</span></td>
                    </tr>
                    <tr>
                        <td>Science</td>
                        <td>40</td>
                        <td>34</td>
                        <td>85%</td>
                        <td><span class="badge bg-success">Above Cutoff</span></td>
                    </tr>
                    <tr>
                        <td>English</td>
                        <td>38</td>
                        <td>28</td>
                        <td>74%</td>
                        <td><span class="badge bg-warning text-dark">Below Cutoff</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Attendance Trend -->
    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-semibold mb-3">Attendance Trend</h5>

        <div style="height: 220px;">
            <canvas id="attendanceChart"></canvas>
        </div>

        <small class="text-muted d-block mt-2">
            Attendance trend over the last few months
        </small>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
@endpush

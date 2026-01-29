<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">
            @if(!empty($student))
                {{ $student->first_name }} {{ $student->last_name }} (Class {{ $student->class_name ?? '-' }})
            @else
                Welcome, {{ Auth::user()->name ?? 'Student Name' }}
            @endif
        </h4>
        <small>{{ now()->format('l, F d, Y') }}</small>
    </div>
 

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <a href="{{ route('student.attendance') }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 p-3 hover-shadow">
                    <small class="text-muted">Attendance</small>
                    <h3 class="fw-bold">
                        @if(!empty($attendance) && isset($attendance['percentage']) && $attendance['percentage'] !== null)
                            {{ $attendance['percentage'] }}%
                        @else
                            -
                        @endif
                    </h3>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <small class="text-muted">Fees Due</small>
                <h3 class="fw-bold {{ (!empty($fees) && ($fees['due'] ?? 0) > 0) ? 'text-danger' : 'text-success' }}">
                    @if(!empty($fees))
                        ₹{{ number_format($fees['due'] ?? 0, 2) }}
                    @else
                        ₹0.00
                    @endif
                </h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <small class="text-muted">Upcoming Exam</small>
                <h6 class="fw-semibold mb-0">
                    {{ $latestResult->exam_name ?? 'N/A' }}
                </h6>
                <small class="text-muted">
                    {{ $latestResult->academic_year ?? '' }}
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <small class="text-muted">Overall Grade</small>
                <h3 class="fw-bold text-success">
                    {{ $latestResult->grade ?? '-' }}
                </h3>
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
                            @forelse($subjectPerformance as $row)
                                <tr>
                                    <td>{{ $row['subject_name'] }}</td>
                                    <td>{{ $row['unit_test'] ?? '-' }}</td>
                                    <td>{{ $row['half_yearly'] ?? '-' }}</td>
                                    <td>{{ $row['annual'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No marks data available yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
 
                @if(!empty($latestResult))
                    <a href="{{ route('marksheet.download', ['resultId' => $latestResult->id]) }}" 
                       class="btn btn-outline-primary btn-sm mt-2" 
                       target="_blank">
                        <i class="bi bi-download"></i> Download Marksheet
                    </a>
                @else
                    <button class="btn btn-outline-primary btn-sm mt-2" disabled>
                        <i class="bi bi-download"></i> Download Marksheet
                    </button>
                @endif
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
 
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
<script src="{{ asset('js/attendance-chart.js') }}"></script>
@endpush
 
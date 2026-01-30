<div class="faculty-dashboard">
    <!-- Compact Header -->
    <div class="dashboard-header rounded-3 px-4 py-3 mb-3 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-semibold text-white">Welcome, {{ Auth::user()->name ?? 'Faculty' }}</h5>
            <small class="opacity-90">{{ now()->format('l, M j, Y') }}</small>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <a href="{{ route('faculty.checkin') }}" class="btn btn-light btn-sm opacity-90">
                <i class="bi bi-clock me-1"></i>Check-In
            </a>
            <a href="{{ route('faculty.attendance') }}" class="btn btn-light btn-sm opacity-90">
                <i class="bi bi-calendar-check me-1"></i>Attendance
            </a>
        </div>
    </div>

    <!-- Stats Row - Compact -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <a href="{{ route('faculty.checkin') }}" class="text-decoration-none">
                <div class="stat-card stat-card--status rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="stat-card__label">Today's Status</small>
                            <div class="stat-card__value mt-0">
                                @if($todayAttendance)
                                    {{ $todayAttendance->status ?? 'Present' }}
                                @else
                                    Not Marked
                                @endif
                            </div>
                            @if($todayAttendance && $todayAttendance->check_in_time)
                                <small class="text-muted">{{ \Carbon\Carbon::parse($todayAttendance->attendance_date.' '.$todayAttendance->check_in_time, config('app.timezone'))->setTimezone('Asia/Kolkata')->format('g:i A') }}</small>
                            @endif
                        </div>
                        <span class="stat-card__icon"><i class="bi bi-person-check"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('faculty.attendance') }}" class="text-decoration-none">
                <div class="stat-card stat-card--attendance rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="stat-card__label">Attendance Today</small>
                            <div class="stat-card__value mt-0">{{ $todayStudentAttendanceMarked }}</div>
                            <small class="text-muted">students</small>
                        </div>
                        <span class="stat-card__icon"><i class="bi bi-calendar-check"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('faculty.marks') }}" class="text-decoration-none">
                <div class="stat-card stat-card--marks rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="stat-card__label">Pending Marks</small>
                            <div class="stat-card__value mt-0 {{ $pendingMarksCount > 0 ? 'text-warning' : 'text-success' }}">{{ $pendingMarksCount }}</div>
                        </div>
                        <span class="stat-card__icon"><i class="bi bi-pencil-square"></i></span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-card--subjects rounded-3 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="stat-card__label">Assigned Subjects</small>
                        <div class="stat-card__value mt-0">{{ count($assignedSubjects) }}</div>
                    </div>
                    <span class="stat-card__icon"><i class="bi bi-journal-bookmark"></i></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content - Two Columns -->
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-transparent border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">Assigned Subjects</h6>
                    <a href="{{ route('faculty.classes') }}" class="btn btn-link btn-sm text-primary py-0">View all <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="card-body pt-0 px-3 pb-3">
                    @if(count($assignedSubjects) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th class="border-0 py-1">Subject</th>
                                        <th class="border-0 py-1">Class</th>
                                        <th class="border-0 py-1">Stream</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignedSubjects as $as)
                                        <tr>
                                            <td class="py-2"><span class="fw-medium">{{ $as->subject_name }}</span></td>
                                            <td class="py-2"><span class="badge bg-light text-dark">{{ $as->class_name }}</span></td>
                                            <td class="py-2 text-muted small">{{ $as->stream_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-0 py-2">No subjects assigned yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-transparent border-0 py-2 px-3">
                    <h6 class="mb-0 fw-semibold">Pending Tasks</h6>
                </div>
                <div class="card-body pt-0 px-3 pb-3">
                    <div class="d-flex flex-column gap-2">
                        @php
                            $hasMarks = $pendingMarksCount > 0;
                            $hasCheckin = !$todayAttendance || !$todayAttendance->check_in_time;
                            $hasAttendance = count($assignedSubjects) > 0 && $todayStudentAttendanceMarked == 0;
                            $allSet = !$hasMarks && !$hasCheckin && !$hasAttendance;
                        @endphp
                        @if($hasMarks)
                            <a href="{{ route('faculty.marks') }}" class="task-item rounded-2 px-3 py-2 text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                <span class="small"><i class="bi bi-pencil text-warning me-2"></i>Enter marks for exams</span>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        @endif
                        @if($hasCheckin)
                            <a href="{{ route('faculty.checkin') }}" class="task-item rounded-2 px-3 py-2 text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                <span class="small"><i class="bi bi-clock text-primary me-2"></i>Check-in for today</span>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        @endif
                        @if($hasAttendance)
                            <a href="{{ route('faculty.attendance') }}" class="task-item rounded-2 px-3 py-2 text-decoration-none text-dark d-flex align-items-center justify-content-between">
                                <span class="small"><i class="bi bi-calendar-check text-info me-2"></i>Mark student attendance</span>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        @endif
                        @if($allSet)
                            <div class="task-item task-item--done rounded-2 px-3 py-2 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <span class="small text-muted">All set for today</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links + Alerts - Single Row -->
    <div class="row g-3 mt-0">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body py-2 px-3">
                    <h6 class="fw-semibold mb-2 small text-uppercase text-muted">Quick Links</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('faculty.attendance') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-calendar-check me-1"></i>Attendance
                        </a>
                        <a href="{{ route('faculty.marks') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-pencil-square me-1"></i>Marks
                        </a>
                        <a href="{{ route('faculty.performance') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-graph-up me-1"></i>Reports
                        </a>
                        <a href="{{ route('faculty.reports') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-file-pdf me-1"></i>PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-semibold mb-0 small text-uppercase text-muted">Alerts</h6>
                        <a href="{{ route('faculty.notifications') }}" class="btn btn-link btn-sm py-0 text-primary">View all</a>
                    </div>
                    <ul class="list-unstyled mb-0 small">
                        <li class="d-flex align-items-center py-1"><i class="bi bi-dot text-primary me-2"></i>Mark attendance for your classes</li>
                        <li class="d-flex align-items-center py-1"><i class="bi bi-dot text-warning me-2"></i>Submit exam results before deadline</li>
                        <li class="d-flex align-items-center py-1"><i class="bi bi-dot text-info me-2"></i>Upcoming exam schedule</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

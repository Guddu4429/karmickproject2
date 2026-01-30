<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            @if($isTeacher || $isPrincipal)
                <!-- Teacher/Admin Profile Image -->
                <img
                    src="{{ asset('assets/profile.jpg') }}"
                    alt="{{ $isPrincipal ? 'Admin' : 'Teacher' }} Profile"
                    class="rounded-circle border-2 border-white"
                    width="55"
                    height="55"
                    style="object-fit: cover;"
                >
            @endif
            <div>
                <h4 class="mb-1">
                    @if($isPrincipal)
                        Welcome, {{ Auth::user()->name ?? 'Admin' }}
                    @elseif($isTeacher)
                        Welcome, {{ Auth::user()->name ?? 'Teacher Name' }}
                    @elseif(!empty($student))
                        {{ $student->first_name }} {{ $student->last_name }} (Class {{ $student->class_name ?? '-' }})
                    @else
                        Welcome, {{ Auth::user()->name ?? 'Student Name' }}
                    @endif
                </h4>
                <small>{{ now()->format('l, F d, Y') }}</small>
            </div>
        </div>

        @if($isTeacher)
            <!-- Check In / Out -->
            <div>
                @if($checkInStatus && $checkInStatus->check_in_time && !$checkInStatus->check_out_time)
                    <button
                        wire:click="checkOut"
                        class="btn btn-checkin btn-sm"
                        title="Click to Check Out"
                    >
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Checked In
                    </button>
                @else
                    <button
                        wire:click="checkIn"
                        id="checkInBtn"
                        class="btn btn-outline-light btn-sm"
                        title="Click to Check In"
                    >
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Check In
                    </button>
                @endif
            </div>
        @endif
    </div>
 

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        @if($isPrincipal)
            <!-- Admin/Principal KPI Cards -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Total Students</small>
                    <h3 class="fw-bold mb-0">{{ $totalStudents }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Total Teachers</small>
                    <h3 class="fw-bold mb-0">{{ $totalTeachers }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">New Admissions</small>
                    <h3 class="fw-bold mb-0">{{ $newAdmissions }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Upcoming Exams</small>
                    <h3 class="fw-bold mb-0">{{ $upcomingExams }}</h3>
                </div>
            </div>
        @elseif($isTeacher)
            <!-- Teacher KPI Cards -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Classes Assigned</small>
                    <h3 class="fw-bold mb-0">{{ $classesAssigned }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Subjects Handled</small>
                    <div class="overflow-auto" style="max-height: 80px;">
                        @if(!empty($subjectsHandled) && count($subjectsHandled) > 0)
                            <ul class="list-unstyled mb-0 small">
                                @foreach($subjectsHandled as $subject)
                                    <li class="mb-1">{{ $subject }}</li>
                                @endforeach
                            </ul>
                        @else
                            <small class="text-muted">No subjects assigned</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Attendance Pending</small>
                    <h3 class="fw-bold text-danger mb-0">{{ $attendancePending }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Upcoming Exams</small>
                    <h3 class="fw-bold mb-0">{{ $upcomingExam }}</h3>
                </div>
            </div>
        @else
            <!-- Student KPI Cards -->
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
        @endif
    </div>
 
    @if($isPrincipal)
        <!-- Latest Enquiries -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Latest Enquiries</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>SL No</th>
                            <th>Enquiries</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enquiries as $enquiry)
                            <tr>
                                <td>{{ $enquiry['sl_no'] }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $enquiry['sl_no'] }}. {{ $enquiry['email_from'] }}</strong><br>
                                        <small class="text-muted">{{ $enquiry['subject'] }}</small>
                                    </div>
                                </td>
                                <td>
                                    <button
                                        wire:click="toggleEnquiryRead({{ $enquiry['id'] }})"
                                        class="btn btn-sm {{ $enquiry['is_read'] ? 'btn-success' : 'btn-warning' }}"
                                    >
                                        {{ $enquiry['is_read'] ? 'Read' : 'Unread' }}
                                    </button>
                                    <button
                                        wire:click="viewEnquiry({{ $enquiry['id'] }})"
                                        class="btn btn-sm btn-outline-primary ms-2"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    No enquiries available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alerts & Notices -->
        <div class="row g-4 mb-4">
            <!-- Attendance Alerts -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <h5 class="fw-semibold mb-3">Attendance Alerts</h5>

                    <ul class="list-group list-group-flush">
                        @forelse($attendanceAlerts as $alert)
                            <li class="list-group-item px-0">{{ $alert }}</li>
                        @empty
                            <li class="list-group-item px-0 text-muted">No alerts</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Admin Notices -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <h5 class="fw-semibold mb-3">Admin Notices</h5>

                    <ul class="list-group list-group-flush">
                        @forelse($adminNotices as $notice)
                            <li class="list-group-item px-0">{{ $notice }}</li>
                        @empty
                            <li class="list-group-item px-0 text-muted">No notices</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @elseif($isTeacher)
        <!-- Today's Teaching Schedule -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Today's Teaching Schedule</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todaySchedule as $schedule)
                            <tr>
                                <td>{{ $schedule['class'] }}</td>
                                <td>{{ $schedule['section'] }}</td>
                                <td>{{ $schedule['subject'] }}</td>
                                <td>{{ $schedule['time'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $schedule['status'] === 'Completed' ? 'success' : 'warning text-dark' }}">
                                        {{ $schedule['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    No schedule available for today.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Alerts & Notices -->
        <div class="row g-4 mb-4">
            <!-- Attendance Alerts -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <h5 class="fw-semibold mb-3">Attendance Alerts</h5>

                    <ul class="list-group list-group-flush">
                        @forelse($attendanceAlerts as $alert)
                            <li class="list-group-item px-0">{{ $alert }}</li>
                        @empty
                            <li class="list-group-item px-0 text-muted">No alerts</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Admin Notices -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-4 p-4 h-100">
                    <h5 class="fw-semibold mb-3">Admin Notices</h5>

                    <ul class="list-group list-group-flush">
                        @forelse($adminNotices as $notice)
                            <li class="list-group-item px-0">{{ $notice }}</li>
                        @empty
                            <li class="list-group-item px-0 text-muted">No notices</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if(!$isTeacher && !$isPrincipal)
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
    @endif

    @if($isTeacher)
        <!-- Check-in Toast -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="checkinToast" class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        ✅ You have successfully checked in.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    @endif

    @if($isPrincipal && $selectedEnquiry)
        <!-- Enquiry Detail Modal -->
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enquiry Details</h5>
                        <button type="button" class="btn-close" wire:click="closeEnquiryView" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Email From</th>
                                <td>{{ $selectedEnquiry->email_from }}</td>
                            </tr>
                            <tr>
                                <th>Email To</th>
                                <td>{{ $selectedEnquiry->email_to }}</td>
                            </tr>
                            <tr>
                                <th>Subject</th>
                                <td>{{ $selectedEnquiry->subject }}</td>
                            </tr>
                            <tr>
                                <th>Message Text</th>
                                <td>{{ $selectedEnquiry->message_text ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Message HTML</th>
                                <td>
                                    @if($selectedEnquiry->message_html)
                                        <div>{!! $selectedEnquiry->message_html !!}</div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ \Carbon\Carbon::parse($selectedEnquiry->created_at)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ \Carbon\Carbon::parse($selectedEnquiry->updated_at)->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeEnquiryView">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
 
@push('scripts')
@if(!$isTeacher && !$isPrincipal)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/attendance-chart.js') }}"></script>
@endif

@if($isTeacher)
<script src="{{ asset('js/script.js') }}"></script>
@endif
@endpush
 
<div>
    {{-- Page Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Teachers Management</h4>
        <button wire:click="openAddModal" class="btn btn-primary">
            + Add Teacher
        </button>
    </div>

    {{-- Filters --}}
    <div class="row g-3 mb-4">
        {{-- Search --}}
        <div class="col-md-6">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       placeholder="Search name / Employee Code / email / phone"
                       wire:model.live.debounce.300ms="search">
            </div>
        </div>

        {{-- Designation --}}
        <div class="col-md-3">
            <select class="form-control" wire:model.live="designation">
                <option value="">All Designations</option>
                @foreach($designations as $desig)
                    <option value="{{ $desig }}">{{ $desig }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Teachers Table --}}
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Teachers List</h5>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Designation</th>
                        <th>Assigned Classes</th>
                        <th>Attendance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $key => $teacher)
                        <tr>
                            <td>{{ $teachers->firstItem() + $key }}</td>
                            <td>{{ $teacher->employee_code }}</td>
                            <td>{{ $teacher->user->name ?? '-' }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->phone }}</td>
                            <td>{{ $teacher->designation }}</td>
                            <td>
                                @php
                                    $assignments = \Illuminate\Support\Facades\DB::table('teacher_subjects')
                                        ->where('teacher_id', $teacher->id)
                                        ->join('streams', 'teacher_subjects.stream_id', '=', 'streams.id')
                                        ->join('classes', 'teacher_subjects.class_id', '=', 'classes.id')
                                        ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                                        ->select('streams.name as stream_name', 'classes.name as class_name', 'subjects.name as subject_name', 'teacher_subjects.date', 'teacher_subjects.time', 'teacher_subjects.status')
                                        ->orderBy('teacher_subjects.date', 'desc')
                                        ->orderBy('teacher_subjects.time', 'desc')
                                        ->get();
                                @endphp
                                @if($assignments->count() > 0)
                                    <small>
                                        @foreach($assignments->take(2) as $assignment)
                                            {{ $assignment->stream_name }} - {{ $assignment->class_name }} - {{ $assignment->subject_name }}
                                            @if($assignment->date)
                                                <br><span class="text-muted">{{ \Carbon\Carbon::parse($assignment->date)->format('d M') }} {{ $assignment->time ? \Carbon\Carbon::parse($assignment->time)->format('H:i') : '' }}</span>
                                                <span class="badge bg-{{ $assignment->status === 'attended' ? 'success' : ($assignment->status === 'absent' ? 'danger' : ($assignment->status === 'cancelled' ? 'secondary' : 'warning')) }} ms-1" style="font-size: 0.7em;">
                                                    {{ ucfirst($assignment->status) }}
                                                </span>
                                            @endif
                                            <br>
                                        @endforeach
                                        @if($assignments->count() > 2)
                                            <span class="text-muted">+{{ $assignments->count() - 2 }} more</span>
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted">No assignments</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $todayAttendance = \Illuminate\Support\Facades\DB::table('teacher_attendance_logs')
                                        ->where('teacher_id', $teacher->id)
                                        ->whereDate('attendance_date', today())
                                        ->first();
                                @endphp
                                @if($todayAttendance)
                                    <span class="badge bg-{{ $todayAttendance->status === 'Present' ? 'success' : ($todayAttendance->status === 'Absent' ? 'danger' : 'warning') }}">
                                        {{ $todayAttendance->status }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Not Marked</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button
                                        wire:click="openAssignModal({{ $teacher->id }})"
                                        class="btn btn-sm btn-info"
                                        title="Assign Classes">
                                        Assign
                                    </button>
                                    <button
                                        wire:click="viewAttendance({{ $teacher->id }})"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View Attendance">
                                        Attendance
                                    </button>
                                    <button
                                        wire:click="deleteTeacher({{ $teacher->id }})"
                                        onclick="confirm('Are you sure you want to delete this teacher?') || event.stopImmediatePropagation()"
                                        class="btn btn-sm btn-danger"
                                        title="Delete">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                No teachers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $teachers->links() }}
    </div>

    {{-- Add Teacher Side Drawer --}}
    @if($showAddModal)
    <div class="offcanvas offcanvas-end show" tabindex="-1" id="addTeacherDrawer" style="visibility: visible;" aria-modal="true" role="dialog">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-semibold">Add New Teacher</h5>
            <button type="button" class="btn-close" wire:click="closeAddModal" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form wire:submit.prevent="addTeacher">
                <div class="mb-3">
                    <label class="form-label">Employee Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="employee_code" required readonly style="background-color: #f8f9fa;">
                    <small class="text-muted">Auto-generated</small>
                    @error('employee_code') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="name" required>
                    @error('name') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" wire:model="email" required>
                    @error('email') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" wire:model="phone" required maxlength="10" pattern="[0-9]{10}" placeholder="10 digits only">
                    <small class="text-muted">Enter 10 digit phone number</small>
                    @error('phone') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-control" wire:model="designation_input" placeholder="e.g., Mathematics Teacher, Science Teacher">
                    @error('designation_input') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="password" required>
                    @error('password') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="password_confirmation" required>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Add Teacher</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeAddModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div class="offcanvas-backdrop fade show" wire:click="closeAddModal" style="opacity: 0.5;"></div>
    @endif

    {{-- Assign Classes Modal --}}
    @if($showAssignModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" wire:click.self="closeAssignModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Classes to Teacher</h5>
                    <button type="button" class="btn-close" wire:click="closeAssignModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="assignClass">
                        <div class="mb-3">
                            <label class="form-label">Class <span class="text-danger">*</span></label>
                            <select class="form-control" wire:model="selectedClassId" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedClassId') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stream <span class="text-danger">*</span></label>
                            <select class="form-control" wire:model.live="selectedStreamId" required>
                                <option value="">Select Stream</option>
                                @foreach($streams as $stream)
                                    <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedStreamId') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                            <select class="form-control" wire:model="selectedSubjectId" required wire:key="subject-select-{{ $selectedStreamId }}" wire:loading.attr="disabled">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            <div wire:loading wire:target="selectedStreamId" class="text-muted small">Loading subjects...</div>
                            @error('selectedSubjectId') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="selectedDate" required min="{{ date('Y-m-d') }}">
                                @error('selectedDate') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" wire:model="selectedTime" required>
                                @error('selectedTime') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeAssignModal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign Class</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- View Attendance Modal --}}
    @if($showAttendanceModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" wire:click.self="closeAttendanceModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Teacher Attendance Records</h5>
                    <button type="button" class="btn-close" wire:click="closeAttendanceModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceRecords as $record)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($record->attendance_date)->format('d M Y') }}</td>
                                        <td>{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '-' }}</td>
                                        <td>{{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $record->status === 'Present' ? 'success' : ($record->status === 'Absent' ? 'danger' : 'warning') }}">
                                                {{ $record->status }}
                                            </span>
                                        </td>
                                        <td>{{ $record->remarks ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">
                                            No attendance records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeAttendanceModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

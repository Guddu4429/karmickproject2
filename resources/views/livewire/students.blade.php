<div>
    {{-- Page Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Students Management</h4>
        <button wire:click="openAddModal" class="btn btn-primary">
            + Add Student
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="grow">
                        <h6 class="text-muted mb-1">Total Students</h6>
                        <h3 class="mb-0 fw-bold">{{ $totalStudents }}</h3>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="grow">
                        <h6 class="text-muted mb-1">Students by Stream</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($studentsPerStream as $stream)
                                <span class="badge bg-primary">{{ $stream->stream_name }}: {{ $stream->count }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="grow">
                        <h6 class="text-muted mb-1">Students by Class</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($studentsPerClass as $class)
                                <span class="badge bg-success">{{ $class->class_name }}: {{ $class->count }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="row g-3 mb-4">
        {{-- Search --}}
        <div class="col-md-4">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       placeholder="Search name / Admission No / Roll No / Phone"
                       wire:model.live.debounce.300ms="search">
            </div>
        </div>

        {{-- Class --}}
        <div class="col-md-2">
            <select class="form-control" wire:model.live="class">
                <option value="">All Classes</option>
                @foreach($classes as $classItem)
                    <option value="{{ $classItem->id }}">{{ $classItem->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Stream --}}
        <div class="col-md-2">
            <select class="form-control" wire:model.live="stream">
                <option value="">All Streams</option>
                @foreach($streams as $streamItem)
                    <option value="{{ $streamItem->id }}">{{ $streamItem->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Year --}}
        <div class="col-md-2">
            <select class="form-control" wire:model.live="year">
                <option value="">All Years</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
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

    {{-- Students Table --}}
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <h5 class="fw-semibold mb-3">Students List</h5>

        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Admission No</th>
                        <th>Name</th>
                        <th>Roll No</th>
                        <th>Class</th>
                        <th>Stream</th>
                        <th>Admission Date</th>
                        <th>Phone</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $key => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $key }}</td>
                            <td>{{ $student->admission_no }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->roll_no ?? '-' }}</td>
                            <td>{{ $student->class ?? '-' }}</td>
                            <td>{{ $student->stream ?? '-' }}</td>
                            <td>{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y') : ($student->created_at ? $student->created_at->format('d M Y') : '-') }}</td>
                            <td>{{ $student->phone ?? '-' }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('student.profile', $student->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="View Profile">
                                        View
                                    </a>
                                    <button
                                        wire:click="deleteStudent({{ $student->id }})"
                                        onclick="confirm('Are you sure you want to delete this student?') || event.stopImmediatePropagation()"
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
                                No students found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $students->links() }}
    </div>

    {{-- Add Student Side Drawer --}}
    @if($showAddModal)
    <div class="offcanvas offcanvas-end show" tabindex="-1" id="addStudentDrawer" style="visibility: visible;" aria-modal="true" role="dialog">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-semibold">Add New Student</h5>
            <button type="button" class="btn-close" wire:click="closeAddModal" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form wire:submit.prevent="addStudent">
                <div class="mb-3">
                    <label class="form-label">Admission No <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="admission_no" required readonly style="background-color: #f8f9fa;">
                    <small class="text-muted">Auto-generated</small>
                    @error('admission_no') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="first_name" required>
                        @error('first_name') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="last_name" required>
                        @error('last_name') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Roll No</label>
                        <input type="text" class="form-control" wire:model="roll_no">
                        @error('roll_no') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" wire:model="dob" required max="{{ date('Y-m-d') }}">
                        @error('dob') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-control" wire:model="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('gender') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guardian <span class="text-danger">*</span></label>
                        <select class="form-control" wire:model="guardian_id" required>
                            <option value="">Select Guardian</option>
                            @foreach($guardians as $guardian)
                                <option value="{{ $guardian->id }}">{{ $guardian->name }}</option>
                            @endforeach
                        </select>
                        @error('guardian_id') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Class <span class="text-danger">*</span></label>
                        <select class="form-control" wire:model="class_id" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $classItem)
                                <option value="{{ $classItem->id }}">{{ $classItem->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stream <span class="text-danger">*</span></label>
                        <select class="form-control" wire:model.live="selectedStreamId" required>
                            <option value="">Select Stream</option>
                            @foreach($streams as $streamItem)
                                <option value="{{ $streamItem->id }}">{{ $streamItem->name }}</option>
                            @endforeach
                        </select>
                        @error('stream_id') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Admission Date</label>
                        <input type="date" class="form-control" wire:model="admission_date" max="{{ date('Y-m-d') }}">
                        <small class="text-muted">Leave empty to use today's date</small>
                        @error('admission_date') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" rows="2" wire:model="address"></textarea>
                        @error('address') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Add Student</button>
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeAddModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div class="offcanvas-backdrop fade show" wire:click="closeAddModal" style="opacity: 0.5;"></div>
    @endif

</div>

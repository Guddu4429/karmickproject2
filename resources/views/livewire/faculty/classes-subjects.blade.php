<div>
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Classes & Subjects</h4>
        <small>View assigned classes, subjects, and student lists</small>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">Assigned Classes & Subjects</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Class</th>
                                <th>Stream</th>
                                <th>Subject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $a)
                                <tr>
                                    <td>{{ $a->class_name }}</td>
                                    <td>{{ $a->stream_name ?? '-' }}</td>
                                    <td>{{ $a->subject_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No assignments yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">Student List</h5>
                <div class="mb-3">
                    <label class="form-label">Select Class</label>
                    <select class="form-select" wire:model.live="selectedClass">
                        <option value="">Select Class</option>
                        @foreach($classOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-sm align-middle">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Stream</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $s)
                                <tr>
                                    <td>{{ $s->roll_no }}</td>
                                    <td>{{ $s->first_name }} {{ $s->last_name }}</td>
                                    <td>{{ $s->stream_name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Select a class to view students.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-semibold mb-3">Timetable</h5>
        <p class="text-muted mb-0">Timetable information will be displayed here once configured.</p>
    </div>
</div>

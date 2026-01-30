<div>
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Student Attendance</h4>
        <small>Mark daily attendance and view attendance reports</small>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select class="form-select" wire:model.live="selectedClass">
                    <option value="">Select Class</option>
                    @foreach($classOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subject</label>
                <select class="form-select" wire:model.live="selectedSubject">
                    <option value="">Select Subject</option>
                    @foreach($subjectOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" wire:model.live="selectedDate">
            </div>
        </div>
    </div>

    @if(count($students) > 0)
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-semibold mb-3">Mark Attendance - {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Stream</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->stream_name ?? '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button"
                                                class="btn {{ ($attendanceRecords[$student->id] ?? '') === 'Present' ? 'btn-success' : 'btn-outline-success' }}"
                                                wire:click="markAttendance({{ $student->id }}, 'Present')">
                                            Present
                                        </button>
                                        <button type="button"
                                                class="btn {{ ($attendanceRecords[$student->id] ?? '') === 'Absent' ? 'btn-danger' : 'btn-outline-danger' }}"
                                                wire:click="markAttendance({{ $student->id }}, 'Absent')">
                                            Absent
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 p-4 mt-4">
            <h5 class="fw-semibold mb-3">Monthly Report ({{ now()->format('F Y') }})</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Present</th>
                            <th>Total</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $monthly = $this->getMonthlyReportProperty(); @endphp
                        @foreach($students as $student)
                            @php
                                $m = $monthly[$student->id] ?? null;
                                $total = $m->total ?? 0;
                                $present = $m->present ?? 0;
                                $pct = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $present }}</td>
                                <td>{{ $total }}</td>
                                <td>
                                    <span class="badge {{ $pct >= 75 ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center text-muted">
            <p class="mb-0">Select a class and subject to mark attendance.</p>
        </div>
    @endif
</div>

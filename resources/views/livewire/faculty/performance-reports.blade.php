<div>
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Performance Reports</h4>
        <small>Subject-wise and class-wise result analysis</small>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Class</label>
                <select class="form-select" wire:model.live="selectedClass">
                    @foreach($classOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Exam</label>
                <select class="form-select" wire:model.live="selectedExam">
                    @foreach($examOptions as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">Subject-wise Performance</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Avg Marks</th>
                                <th>Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjectWisePerformance as $s)
                                <tr>
                                    <td>{{ $s->subject_name }}</td>
                                    <td>{{ $s->avg_marks }}</td>
                                    <td>{{ $s->student_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h5 class="fw-semibold mb-3">Class-wise Result Summary</h5>
                @if(!empty($classWiseSummary) && ($classWiseSummary->student_count ?? 0) > 0)
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted">Average %</small>
                            <h4 class="fw-bold mb-0">{{ round($classWiseSummary->avg_pct, 2) }}%</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Students</small>
                            <h4 class="fw-bold mb-0">{{ $classWiseSummary->student_count }}</h4>
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0">No result summary available.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-semibold mb-3">Weak Students (Below 40)</h5>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weakStudents as $w)
                        <tr>
                            <td>{{ $w->roll_no }}</td>
                            <td>{{ $w->first_name }} {{ $w->last_name }}</td>
                            <td>{{ $w->subject_name }}</td>
                            <td><span class="badge bg-danger">{{ $w->marks_obtained }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No weak students identified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <small class="text-muted">Download reports may be enabled by admin.</small>
    </div>
</div>

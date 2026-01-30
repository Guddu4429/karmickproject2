<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Exams & Results</h4>
        <small>
            @if(!empty($student))
                {{ $student->first_name }} {{ $student->last_name }} • Class {{ $student->class_name ?? '-' }} {{ $student->stream_name ? '('.$student->stream_name.')' : '' }}
            @else
                View examination schedule and academic performance
            @endif
        </small>
    </div>

    @if (empty($student))
        <div class="alert alert-info">
            Please select a student first.
        </div>
    @else
        <!-- Exam Selector -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Academic Year</label>
                    <select class="form-select" wire:model.live="selectedYear">
                        @foreach($yearOptions as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Examination</label>
                    <select class="form-select" wire:model.live="selectedExamName">
                        @if(count($examNameOptions) === 0)
                            <option value="">No exams available</option>
                        @else
                            @foreach($examNameOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    @if($selectedResult)
                        <label class="form-label fw-semibold d-block">Result Summary</label>
                        <div class="d-flex gap-2">
                            <div class="flex-fill text-center p-2 bg-light rounded">
                                <small class="text-muted d-block">Percentage</small>
                                <strong class="text-primary">{{ $selectedResult->percentage }}%</strong>
                            </div>
                            <div class="flex-fill text-center p-2 bg-light rounded">
                                <small class="text-muted d-block">Grade</small>
                                <strong class="text-success">{{ $selectedResult->grade }}</strong>
                            </div>
                            <div class="flex-fill text-center p-2 bg-light rounded">
                                <small class="text-muted d-block">Total</small>
                                <strong>{{ $selectedResult->total_marks }}</strong>
                            </div>
                        </div>
                    @else
                        <label class="form-label fw-semibold d-block opacity-0">-</label>
                        <div class="alert alert-info mb-0 py-2">
                            <small><i class="bi bi-info-circle me-1"></i>No result available for this exam</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Exam Results Table -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Subject-wise Marks</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Marks Obtained</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjectMarks as $row)
                            <tr>
                                <td>{{ $row['subject_name'] }}</td>
                                <td><span class="badge bg-primary">{{ $row['marks'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">
                                    @if($selectedExamName)
                                        No marks recorded for this exam yet.
                                    @else
                                        Please select a year and exam to view marks.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($selectedResult)
                <div class="text-end mt-3">
                    <a href="{{ route('marksheet.download', ['resultId' => $selectedResult->id]) }}" 
                       class="btn btn-primary btn-sm" 
                       target="_blank">
                        <i class="bi bi-download me-1"></i>Download Marksheet
                    </a>
                </div>
            @endif
        </div>

        <!-- All Marksheets / Results list -->
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-semibold mb-3">All Results History</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Exam</th>
                            <th>Academic Year</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marksheets as $res)
                            <tr>
                                <td>{{ $res->exam_name }}</td>
                                <td>{{ $res->academic_year }}</td>
                                <td>{{ $res->percentage }}%</td>
                                <td>{{ $res->grade }}</td>
                                <td>
                                    <a href="{{ route('marksheet.download', ['resultId' => $res->id]) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       target="_blank">
                                        <i class="bi bi-download"></i> Download PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    No results available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

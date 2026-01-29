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
        <!-- Exam Summary -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Latest Exam</small>
                    <h5 class="fw-semibold mb-0">
                        {{ $latestResult->exam_name ?? 'N/A' }}
                    </h5>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Total Marks</small>
                    <h5 class="fw-semibold mb-0">
                        {{ isset($latestResult) ? $latestResult->total_marks : 'N/A' }}
                    </h5>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Percentage</small>
                    <h5 class="fw-semibold mb-0">
                        {{ isset($latestResult) ? $latestResult->percentage.'%' : 'N/A' }}
                    </h5>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Grade</small>
                    <h5 class="fw-semibold mb-0">
                        {{ $latestResult->grade ?? 'N/A' }}
                    </h5>
                </div>
            </div>
        </div>

        <!-- Exam Results Table -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Subject-wise Marks (Latest Exam)</h5>

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
                                <td>{{ $row['marks'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">
                                    No marks recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Exam Schedule (simple list of exams for the class) -->
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
            <h5 class="fw-semibold mb-3">Examinations ({{ $student->class_name ?? '-' }})</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Exam</th>
                            <th>Academic Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingExams as $exam)
                            <tr>
                                <td>{{ $exam['name'] }}</td>
                                <td>{{ $exam['academic_year'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">
                                    No exams configured yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Marksheet Download / Results list -->
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-semibold mb-3">Digital Marksheets</h5>

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

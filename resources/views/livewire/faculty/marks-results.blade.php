<div>
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Marks & Results</h4>
        <small>Enter and update marks, view result summary</small>
    </div>

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
                <label class="form-label">Stream</label>
                <select class="form-select" wire:model.live="selectedStream">
                    <option value="">Select Stream</option>
                    @foreach($streamOptions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Exam</label>
                <select class="form-select" wire:model.live="selectedExam">
                    <option value="">Select Exam</option>
                    @foreach($examOptions as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
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
        </div>
    </div>

    @if(!empty($resultSummary))
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-3">
                    <small class="text-muted">Marks Entered</small>
                    <h5 class="fw-bold mb-0">{{ $resultSummary['marks_entered'] }}/{{ $resultSummary['total_students'] }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-3">
                    <small class="text-muted">Average</small>
                    <h5 class="fw-bold mb-0">{{ $resultSummary['average'] }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-3">
                    <small class="text-muted">Highest</small>
                    <h5 class="fw-bold mb-0">{{ $resultSummary['highest'] }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-4 p-3">
                    <small class="text-muted">Lowest</small>
                    <h5 class="fw-bold mb-0">{{ $resultSummary['lowest'] }}</h5>
                </div>
            </div>
        </div>
    @endif

    @if(count($students) > 0)
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-semibold mb-3">Enter Marks</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td style="width: 120px;">
                                    <input type="number" min="0" max="100" step="1"
                                           class="form-control form-control-sm"
                                           wire:model.defer="marks.{{ $student->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary btn-sm" wire:click="saveAllMarks">
                    <span wire:loading wire:target="saveAllMarks">Saving...</span>
                    <span wire:loading.remove wire:target="saveAllMarks">Save All Marks</span>
                </button>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center text-muted">
            <p class="mb-0">Select class, stream, exam, and subject to enter marks.</p>
        </div>
    @endif
</div>

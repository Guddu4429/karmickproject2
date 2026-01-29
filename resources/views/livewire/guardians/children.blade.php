<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center mb-4">
        <h3 class="mb-1 fw-bold">My Children</h3>
        <div class="text-muted">Select a student to open their dashboard</div>
    </div>

    <div class="w-100" style="max-width: 960px;">
        @if (!$guardianId)
            <div class="alert alert-warning text-center">
                No guardian profile found for this account.
            </div>
        @elseif ($children->isEmpty())
            <div class="alert alert-info text-center">
                No students are linked to this guardian yet.
            </div>
        @else
            <div class="row g-4 justify-content-center">
                @foreach ($children as $child)
                    <div class="col-sm-10 col-md-6 col-lg-4 d-flex">
                        <a href="{{ route('guardian.student.dashboard', ['student' => $child->id]) }}"
                           class="text-decoration-none flex-grow-1">
                            <div class="card shadow-sm border-0 rounded-4 h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                        <div>
                                            <h5 class="mb-1 text-dark">
                                                {{ $child->first_name }} {{ $child->last_name }}
                                            </h5>
                                            <div class="text-muted small">
                                                Class {{ $child->class_name ?? '-' }}
                                                @if(!empty($child->stream_name))
                                                    • {{ $child->stream_name }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-primary fs-4">
                                            <i class="bi bi-arrow-right-circle"></i>
                                        </div>
                                    </div>

                                    <hr class="my-2">

                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Admission No.</span>
                                        <span class="text-dark fw-semibold">{{ $child->admission_no }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span>Roll</span>
                                        <span class="text-dark fw-semibold">{{ $child->roll_no ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>


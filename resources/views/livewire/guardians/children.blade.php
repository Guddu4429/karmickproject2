<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">My Children</h3>
            <div class="text-muted">Select a student to open their dashboard</div>
        </div>
    </div>

    @if (!$guardianId)
        <div class="alert alert-warning">
            No guardian profile found for this account.
        </div>
    @elseif ($children->isEmpty())
        <div class="alert alert-info">
            No students are linked to this guardian yet.
        </div>
    @else
        <div class="row g-4">
            @foreach ($children as $child)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('guardian.student.dashboard', ['student' => $child->id]) }}"
                       class="text-decoration-none">
                        <div class="card shadow-sm border-0 rounded-4 h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between">
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
                                    <div class="text-primary">
                                        <i class="bi bi-arrow-right-circle"></i>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Admission</span>
                                    <span class="text-dark">{{ $child->admission_no }}</span>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Roll</span>
                                    <span class="text-dark">{{ $child->roll_no ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>


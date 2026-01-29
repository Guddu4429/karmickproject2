<div>
    <!-- Header -->
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Fees</h4>
        <small>
            @if(!empty($student))
                {{ $student->first_name }} {{ $student->last_name }} • Class {{ $student->class_name ?? '-' }} {{ $student->stream_name ? '('.$student->stream_name.')' : '' }}
            @else
                View your fee details and payment history
            @endif
        </small>
    </div>

    @if (empty($student))
        <div class="alert alert-info">
            Please select a student first.
        </div>
    @else
        <!-- Fees Summary -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Total Fees For Academic Year 2026</small>
                    <h3 class="fw-bold mb-0">₹{{ number_format($summary['annual_total'] ?? 0, 2) }}</h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Paid Amount</small>
                    <h3 class="fw-bold text-success mb-0">₹{{ number_format($summary['paid'] ?? 0, 2) }}</h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <small class="text-muted">Due Amount</small>
                    <h3 class="fw-bold {{ ($summary['due'] ?? 0) > 0 ? 'text-danger' : 'text-success' }} mb-0">
                        ₹{{ number_format($summary['due'] ?? 0, 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h5 class="fw-semibold mb-3">Payment History</h5>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Receipt No</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</td>
                                <td>{{ $p->receipt_no }}</td>
                                <td>₹{{ number_format($p->amount, 2) }}</td>
                                <td>{{ $p->mode }}</td>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">
                                    No fee payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

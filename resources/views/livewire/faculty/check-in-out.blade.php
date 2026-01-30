<div>
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Check-In / Check-Out</h4>
        <small>Record your attendance and view working hours</small>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <small class="text-muted">Today's Status</small>
                <h4 class="fw-bold mb-2">{{ $todayLog->status ?? 'Not Marked' }}</h4>
                @if($todayLog)
                    <p class="mb-1 small">Check-in: {{ $todayLog->check_in_time ? \Carbon\Carbon::parse($todayLog->attendance_date.' '.$todayLog->check_in_time, config('app.timezone'))->setTimezone('Asia/Kolkata')->format('g:i A') : '-' }}</p>
                    <p class="mb-0 small">Check-out: {{ $todayLog->check_out_time ? \Carbon\Carbon::parse($todayLog->attendance_date.' '.$todayLog->check_out_time, config('app.timezone'))->setTimezone('Asia/Kolkata')->format('g:i A') : '-' }}</p>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <small class="text-muted">Total Working Hours</small>
                <h4 class="fw-bold mb-0">{{ $totalWorkingHours }} hrs</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <small class="text-muted">Actions</small>
                <div class="d-flex gap-2 mt-2">
                    @if(!$todayLog || !$todayLog->check_in_time)
                        <button class="btn btn-success" wire:click="checkIn">Check-In</button>
                    @endif
                    @if($todayLog && $todayLog->check_in_time && !$todayLog->check_out_time)
                        <button class="btn btn-danger" wire:click="checkOut">Check-Out</button>
                    @endif
                    @if($todayLog && $todayLog->check_out_time)
                        <span class="text-success fw-semibold"><i class="bi bi-check-circle"></i> Done for today</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <h5 class="fw-semibold mb-3">Attendance History</h5>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Status</th>
                        <th>Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendanceHistory as $log)
                        @php
                            $hours = '-';
                            if ($log->check_in_time && $log->check_out_time) {
                                $in = \Carbon\Carbon::parse($log->check_in_time);
                                $out = \Carbon\Carbon::parse($log->check_out_time);
                                $hours = round($out->diffInMinutes($in) / 60, 1) . ' hrs';
                            }
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->attendance_date)->format('d M Y') }}</td>
                            <td>{{ $log->check_in_time ? \Carbon\Carbon::parse($log->attendance_date.' '.$log->check_in_time, config('app.timezone'))->setTimezone('Asia/Kolkata')->format('g:i A') : '-' }}</td>
                            <td>{{ $log->check_out_time ? \Carbon\Carbon::parse($log->attendance_date.' '.$log->check_out_time, config('app.timezone'))->setTimezone('Asia/Kolkata')->format('g:i A') : '-' }}</td>
                            <td><span class="badge bg-{{ $log->status === 'Present' ? 'success' : ($log->status === 'Absent' ? 'danger' : 'warning') }}">{{ $log->status }}</span></td>
                            <td>{{ $hours }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No attendance records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div>
    <div class="bg-primary text-white rounded-4 p-4 mb-4">
        <h4 class="mb-1">Reports / PDF</h4>
        <small>Download attendance reports, result sheets, and class reports</small>
    </div>

    <div class="row g-4">
        @foreach($reports as $report)
            <div class="col-md-6 col-lg-3">
                <a href="{{ route($report['route']) }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 rounded-4 p-4 hover-shadow h-100">
                        <div class="mb-2">
                            <i class="bi {{ $report['icon'] }} fs-2 text-primary"></i>
                        </div>
                        <h6 class="fw-semibold mb-1">{{ $report['name'] }}</h6>
                        <small class="text-muted">{{ $report['desc'] }}</small>
                        <div class="mt-2">
                            <span class="text-primary small">View Report <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4 mt-4">
        <h5 class="fw-semibold mb-3">PDF Download</h5>
        <p class="text-muted mb-0">PDF export functionality can be enabled for authorized reports. Contact admin for access.</p>
    </div>
</div>

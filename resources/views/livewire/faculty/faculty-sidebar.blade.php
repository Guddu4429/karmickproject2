<aside class="col-lg-3 col-xl-2">
    <div class="bg-white h-100 rounded-4 p-3 shadow-sm d-flex flex-column">
        <h5 class="fw-bold mb-4 text-primary">DPS Ruby Park</h5>
        <small class="text-muted mb-3 d-block">Faculty Portal</small>

        <ul class="nav flex-column gap-2">
            <li>
                <a class="nav-link {{ $activeMenu === 'dashboard' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.dashboard') }}">
                    <i class="bi bi-house me-2"></i>Dashboard
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'attendance' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.attendance') }}">
                    <i class="bi bi-calendar-check me-2"></i>Student Attendance
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'checkin' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.checkin') }}">
                    <i class="bi bi-clock me-2"></i>Check-In / Check-Out
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'marks' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.marks') }}">
                    <i class="bi bi-pencil-square me-2"></i>Marks & Results
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'classes' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.classes') }}">
                    <i class="bi bi-journal-bookmark me-2"></i>Classes & Subjects
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'performance' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.performance') }}">
                    <i class="bi bi-graph-up me-2"></i>Performance Reports
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'reports' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.reports') }}">
                    <i class="bi bi-file-pdf me-2"></i>Reports / PDF
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'notifications' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.notifications') }}">
                    <i class="bi bi-bell me-2"></i>Notifications
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'profile' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                   href="{{ route('faculty.profile') }}">
                    <i class="bi bi-person me-2"></i>Profile & Settings
                </a>
            </li>
        </ul>

        <div class="mt-auto pt-3 border-top">
            <button wire:click="logout" class="nav-link w-100 text-start text-danger border-0 bg-transparent">
                <i class="bi bi-box-arrow-left me-2"></i>Logout
            </button>
        </div>
    </div>
</aside>

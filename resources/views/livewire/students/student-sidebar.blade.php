<aside class="col-lg-3 col-xl-2">
    <div class="bg-white h-100 rounded-4 p-3 shadow-sm d-flex flex-column">
        <h5 class="fw-bold mb-4 text-primary">DPS Ruby Park</h5>

        <ul class="nav flex-column gap-2">
            <li>
                <a class="nav-link {{ $activeMenu === 'dashboard' ? 'active text-primary fw-semibold' : 'text-secondary' }}" 
                   href="{{ route('student.dashboard') }}">
                    <i class="bi bi-house me-2"></i>Dashboard
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'profile' ? 'active text-primary fw-semibold' : 'text-secondary' }}" 
                   href="{{ route('student.profile') }}">
                    <i class="bi bi-person me-2"></i>Profile
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'attendance' ? 'active text-primary fw-semibold' : 'text-secondary' }}" 
                   href="{{ route('student.attendance') }}">
                    <i class="bi bi-calendar-check me-2"></i>Attendance
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'fees' ? 'active text-primary fw-semibold' : 'text-secondary' }}" 
                   href="{{ route('student.fees') }}">
                    <i class="bi bi-cash-stack me-2"></i>Fees
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'exams' ? 'active text-primary fw-semibold' : 'text-secondary' }}" 
                   href="{{ route('student.exams') }}">
                    <i class="bi bi-journal-text me-2"></i>Exams
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'notifications' ? 'active text-primary fw-semibold' : 'text-secondary' }}" 
                   href="{{ route('student.notifications') }}">
                    <i class="bi bi-bell me-2"></i>Notifications
                </a>
            </li>
            <li>
                <a class="nav-link {{ $activeMenu === 'settings' ? 'active text-primary fw-semibold' : 'text-secondary' }}" 
                   href="{{ route('student.settings') }}">
                    <i class="bi bi-gear me-2"></i>Settings
                </a>
            </li>
        </ul>

        <!-- Logout Button -->
        <div class="mt-auto pt-3 border-top">
            <button wire:click="logout" class="nav-link w-100 text-start text-danger border-0 bg-transparent">
                <i class="bi bi-box-arrow-left me-2"></i>Logout
            </button>
        </div>
    </div>
</aside>

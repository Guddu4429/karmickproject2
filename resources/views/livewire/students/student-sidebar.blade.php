<aside class="col-lg-3 col-xl-2">
    <div class="bg-white h-100 rounded-4 p-3 shadow-sm d-flex flex-column">
        <h5 class="fw-bold mb-4 text-primary">DPS Ruby Park</h5>

        <ul class="nav flex-column gap-2">
            {{-- Dashboard --}}
            <li>
                <a
                    class="nav-link {{ $activeMenu === 'dashboard' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                    href="{{ $isGuardian && $studentId ? route('guardian.student.dashboard', ['student' => $studentId]) : route('student.dashboard') }}"
                >
                    <i class="bi bi-house me-2"></i>Dashboard
                </a>
            </li>

            {{-- Profile --}}
            <li>
                <a
                    class="nav-link {{ $activeMenu === 'profile' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                    href="{{ $isGuardian && $studentId ? route('student.profile', ['student' => $studentId]) : route('student.profile') }}"
                >
                    <i class="bi bi-person me-2"></i>Profile
                </a>
            </li>

            {{-- Attendance --}}
            <li>
                <a
                    class="nav-link {{ $activeMenu === 'attendance' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                    href="{{ $isGuardian && $studentId ? route('student.attendance', ['student' => $studentId]) : route('student.attendance') }}"
                >
                    <i class="bi bi-calendar-check me-2"></i>Attendance
                </a>
            </li>

            {{-- Fees --}}
            <li>
                <a
                    class="nav-link {{ $activeMenu === 'fees' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                    href="{{ $isGuardian && $studentId ? route('student.fees', ['student' => $studentId]) : route('student.fees') }}"
                >
                    <i class="bi bi-cash-stack me-2"></i>Fees
                </a>
            </li>

            {{-- Exams --}}
            <li>
                <a
                    class="nav-link {{ $activeMenu === 'exams' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                    href="{{ $isGuardian && $studentId ? route('student.exams', ['student' => $studentId]) : route('student.exams') }}"
                >
                    <i class="bi bi-journal-text me-2"></i>Exams
                </a>
            </li>

            {{-- Notifications --}}
            <li>
                <a
                    class="nav-link {{ $activeMenu === 'notifications' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                    href="{{ $isGuardian && $studentId ? route('student.notifications', ['student' => $studentId]) : route('student.notifications') }}"
                >
                    <i class="bi bi-bell me-2"></i>Notifications
                </a>
            </li>

            {{-- Settings --}}
            <li>
                <a
                    class="nav-link {{ $activeMenu === 'settings' ? 'active text-primary fw-semibold' : 'text-secondary' }}"
                    href="{{ $isGuardian && $studentId ? route('student.settings', ['student' => $studentId]) : route('student.settings') }}"
                >
                    <i class="bi bi-gear me-2"></i>Settings
                </a>
            </li>

            {{-- Switch profile for guardians --}}
            @if(!empty($isGuardian))
                <li class="mt-2">
                    <a class=" nav-link text-secondary" href="{{ route('guardian.children') }}">
                        <i class="bi bi-people me-2"></i>
                        Switch Profile
                    </a>
                </li>
            @endif
        </ul>

        <!-- Logout Button -->
        <div class="mt-auto pt-3 border-top">
            <button wire:click="logout" class="nav-link w-100 text-start text-danger border-0 bg-transparent">
                <i class="bi bi-box-arrow-left me-2"></i>Logout
            </button>
        </div>
    </div>
</aside>

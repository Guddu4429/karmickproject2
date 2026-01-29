<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Student Portal') - DPS Ruby Park</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/student_dashboard.css') }}">

    @livewireStyles
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @php
                $currentRoute = request()->route()->getName();
                $activeMenu = 'dashboard';
                
                if (str_contains($currentRoute, 'profile')) {
                    $activeMenu = 'profile';
                } elseif (str_contains($currentRoute, 'attendance')) {
                    $activeMenu = 'attendance';
                } elseif (str_contains($currentRoute, 'fees')) {
                    $activeMenu = 'fees';
                } elseif (str_contains($currentRoute, 'exams')) {
                    $activeMenu = 'exams';
                } elseif (str_contains($currentRoute, 'notifications')) {
                    $activeMenu = 'notifications';
                } elseif (str_contains($currentRoute, 'settings')) {
                    $activeMenu = 'settings';
                }
            @endphp
            
            <livewire:students.student-sidebar active-menu="{{ $activeMenu }}" />

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                <div class="py-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @livewireScripts
</body>
</html>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Vehicle System')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- Select2 Bootstrap5 Theme --}}
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        body { font-family: 'Sarabun', sans-serif; } 
        .navbar-brand { font-size: 0.9rem; }
        @media (min-width: 768px) {
            .navbar-brand { font-size: 1.25rem; }
        }
    </style>
</head>

<body class="bg-light">
    @if (session('admin_id') || session('security_id') || session('user_id') || Auth::check())
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-3 shadow-sm sticky-top">
            <div class="container">
                {{-- Logo / ชื่อระบบ --}}
                <a class="navbar-brand fw-bold text-primary text-wrap" href="#">
                    <i class="bi bi-shield-check me-2"></i>
                    ระบบตรวจสอบทะเบียนยานพาหนะ หอพัก มทร.อีสาน
                </a>

                {{-- ปุ่มออกจากระบบ --}}
                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
                    </button>
                </form>
            </div>
        </nav>
    @endif

    <div class="container">
        @yield('content')
    </div>

    {{-- ======================= Global JS ======================= --}}
    {{-- jQuery (Select2 ต้องใช้) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')

</body>
</html>
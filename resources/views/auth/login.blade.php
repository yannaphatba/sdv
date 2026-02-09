@extends('layouts.app')
@section('title', 'เข้าสู่ระบบ')

@section('content')

<div class="login-bg-fixed"></div>

{{-- ส่วนเนื้อหา --}}
<div class="login-content-wrapper">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            
            <div class="col-11 col-md-8 col-lg-5 col-xl-4">
                
                {{-- การ์ด Login --}}
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden login-card">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary mb-1">เข้าสู่ระบบ</h3>
                            <small class="text-muted">ระบบตรวจสอบทะเบียนยานพาหนะ</small>
                        </div>

                        {{-- Alert แจ้งเตือน --}}
                        @if(session('error'))
                            <div class="alert alert-danger d-flex align-items-center shadow-sm rounded-3 small mb-3">
                                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        @endif

                        {{-- 
                        ================================================================
                        ❌ ซ่อนปุ่ม SSO และตัวคั่น ไว้ก่อน (เพราะยังไม่ได้ทำระบบนี้)
                        ================================================================
                        --}}
                        {{-- 
                        <div class="mb-4">
                            <a href="{{ route('saml2_login', 'rmuti') }}" class="btn btn-outline-primary w-100 py-2 rounded-pill fw-bold shadow-sm hover-scale d-flex align-items-center justify-content-center gap-2">
                                <img src="{{ asset('images/Logo_rmuti.png') }}" width="24" height="auto" alt="Logo">
                                เข้าสู่ระบบด้วยบัญชีนักศึกษา
                            </a>
                        </div>

                        <div class="position-relative my-4 text-center">
                            <hr class="text-muted opacity-25">
                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted x-small">
                                หรือ เจ้าหน้าที่
                            </span>
                        </div>
                        --}}
                        {{-- ================================================================ --}}

                        {{-- ฟอร์ม Login --}}
                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf

                            {{-- ✅ แก้ไข: เพิ่มคำว่า รหัสนักศึกษา เข้าไปใน Label --}}
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control rounded-3 bg-light border-0" id="floatingInput" placeholder="ชื่อผู้ใช้" value="{{ old('username') }}" required>
                                <label for="floatingInput" class="text-muted">
                                    <i class="bi bi-person-fill me-1"></i> ชื่อผู้ใช้ / รหัสนักศึกษา
                                </label>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" name="password" class="form-control rounded-3 bg-light border-0" id="floatingPassword" placeholder="รหัสผ่าน" required>
                                <label for="floatingPassword" class="text-muted">
                                    <i class="bi bi-key-fill me-1"></i> รหัสผ่าน
                                </label>
                            </div>

                            {{-- ✅ แก้ไข: เปลี่ยนข้อความปุ่มให้เป็นกลาง --}}
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow hover-scale">
                                เข้าสู่ระบบ
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <span class="text-muted small">ยังไม่มีบัญชี?</span>
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold ms-1">ลงทะเบียน</a>
                        </div>

                    </div>
                </div>

                <div class="text-center mt-4 text-white opacity-75 small text-shadow">
                    &copy; {{ date('Y') }} RMUTI Vehicle System<br>
                    มหาวิทยาลัยเทคโนโลยีราชมงคลอีสาน
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* พื้นหลังเต็มจอ */
    .login-bg-fixed {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        z-index: -1;
        
        background-image: url("/sdv/images/login.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    
    /* ฟิล์มดำทับพื้นหลัง */
    .login-bg-fixed::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: rgba(0, 0, 0, 0.4);
    }

    /* Wrapper เนื้อหา */
    .login-content-wrapper {
        position: relative;
        z-index: 1;
        width: 100%;
        min-height: 100vh;
    }

    /* CSS */
    .text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.8); }
    .x-small { font-size: 0.8rem; }
    .hover-scale { transition: transform 0.2s ease; }
    .hover-scale:hover { transform: translateY(-3px); }
    
    .form-control:focus {
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
    }
</style>
@endsection
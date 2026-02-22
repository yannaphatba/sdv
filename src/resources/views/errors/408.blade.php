@extends('layouts.app')

@section('title', 'หมดเวลาในการร้องขอ')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-4 p-md-5">
                    <h1 class="display-6 fw-bold text-danger mb-3">หมดเวลาในการร้องขอ (408)</h1>
                    <p class="text-muted mb-2">ไม่สามารถดำเนินการสมัครสมาชิกได้</p>
                    <p class="mb-4">กรุณาใช้อีเมลที่ถูกต้องและต้องลงท้ายด้วย <strong>@rmuti.ac.th</strong> เท่านั้น แล้วลองใหม่อีกครั้ง</p>

                    <a href="{{ route('register') }}" class="btn btn-primary me-2">กลับไปหน้าสมัครสมาชิก</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary">ไปหน้าเข้าสู่ระบบ</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

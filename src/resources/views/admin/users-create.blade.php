@extends('layouts.app')
@section('title', 'เพิ่มผู้ใช้งานใหม่')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            
            <div class="card shadow rounded-4 border-0 overflow-hidden">
                {{-- Header สีน้ำเงิน --}}
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-person-plus-fill me-2"></i>สร้างบัญชีผู้ใช้ใหม่</h4>
                    <small class="opacity-75">กรอกข้อมูลเพื่อเพิ่มผู้ดูแลระบบหรือ รปภ.</small>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">

                    {{-- แจ้งเตือน Error --}}
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm rounded-3">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        {{-- 1. Username --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">ชื่อผู้ใช้ (Username)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-primary"><i class="bi bi-person-fill"></i></span>
                                <input type="text" name="username" class="form-control border-start-0 bg-light no-autofill-bg" 
                                       placeholder="ตั้งชื่อผู้ใช้ (ภาษาอังกฤษ)" value="{{ old('username') }}" required autofocus>
                            </div>
                        </div>

                        {{-- 2. Password --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">รหัสผ่าน (Password)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-primary"><i class="bi bi-key-fill"></i></span>
                                <input type="password" name="password" class="form-control border-start-0 bg-light no-autofill-bg" 
                                       placeholder="กำหนดรหัสผ่านอย่างน้อย 4 ตัวอักษร" required>
                            </div>
                        </div>

                        {{-- 3. Role Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">ตำแหน่ง / สิทธิ์ (Role)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-primary"><i class="bi bi-shield-lock-fill"></i></span>
                                <select name="role" class="form-select border-start-0 bg-light" required>
                                    <option value="">-- กรุณาเลือกตำแหน่ง --</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ)</option>
                                    <option value="security" {{ old('role') == 'security' ? 'selected' : '' }}>Security (เจ้าหน้าที่ รปภ.)</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Buttons --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-lg rounded-pill px-4 shadow-sm">
                                ย้อนกลับ
                            </a>
                            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 shadow fw-bold flex-grow-1">
                                <i class="bi bi-save me-2"></i>บันทึกข้อมูล
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px #f8f9fa inset !important;
        -webkit-text-fill-color: #000 !important;
        transition: background-color 5000s ease-in-out 0s;
    }
</style>

@endsection
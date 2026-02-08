@extends('layouts.app')
@section('title', 'แก้ไขผู้ใช้')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            
            <div class="card shadow rounded-4 border-0 overflow-hidden">
                {{-- Header --}}
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">แก้ไขข้อมูลผู้ใช้งาน</h4>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">

                    {{-- ✅ แก้ไขตรงนี้: ส่งแค่ $user->id ตัวเดียว --}}
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Username --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Username</label>
                            <input type="text" name="username" class="form-control bg-light" 
                                   value="{{ old('username', $user->username) }}" required>
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Password</label>
                            <input type="password" name="password" class="form-control bg-light" 
                                   placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยน">
                            <small class="text-muted" style="font-size: 0.8rem;">* กรอกเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่าน</small>
                        </div>

                        {{-- Role --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Role</label>
                            <select name="role" class="form-select bg-light" required>
                                {{-- ใช้ $user->role เพื่อเช็คค่าเดิม --}}
                                <option value="student"  {{ $user->role =='student' ? 'selected' : '' }}>student</option>
                                <option value="security" {{ $user->role =='security' ? 'selected' : '' }}>security</option>
                                <option value="admin"    {{ $user->role =='admin' ? 'selected' : '' }}>admin</option>
                            </select>
                        </div>

                        <hr class="my-4">

                        {{-- ปุ่มกด --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-lg rounded-pill px-4 shadow-sm">
                                ย้อนกลับ
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold flex-grow-1">
                                บันทึกการเปลี่ยนแปลง
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
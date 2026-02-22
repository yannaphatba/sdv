@extends('layouts.app')
@section('title','สมัครสมาชิก')
@section('content')
<div class="container mt-5" style="max-width:400px;">
  <h3 class="mb-3 text-center">สมัครสมาชิก</h3>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('register.post') }}">
    @csrf

    <div class="mb-3">
      <label>ชื่อผู้ใช้ (ให้ใช้เป็นรหัสนักศึกษา)</label>
      <input type="text" name="username" class="form-control" placeholder="เช่น 6xxxxxxxxxx-x" value="{{ old('username') }}" required>
    </div>

    <div class="mb-3">
      <label>อีเมล</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}" pattern="^[A-Za-z0-9._%+-]+@rmuti\.ac\.th$" title="กรุณาใช้อีเมลที่ลงท้ายด้วย @rmuti.ac.th" required>
      <small class="text-muted">กรอกได้เฉพาะอีเมลที่ลงท้ายด้วย @rmuti.ac.th</small>
    </div>

    <div class="mb-3">
      <label>รหัสผ่าน</label>
      <input type="password" name="password" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>ยืนยันรหัสผ่าน</label>
      <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <input type="hidden" name="role" value="student">

    <button type="submit" class="btn btn-success w-100">สมัครสมาชิก</button>
  </form>

  <div class="text-center mt-3">
    <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
  </div>
</div>
@endsection
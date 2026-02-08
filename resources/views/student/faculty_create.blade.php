@extends('layouts.app')
@section('title', 'เพิ่มคณะใหม่')
@section('content')
<div class="container mt-4 d-flex justify-content-center">
    <div class="card shadow-sm" style="max-width: 500px; width: 100%;">
        <div class="card-body p-4">
            <h4 class="mb-4 text-center fw-bold text-primary">เพิ่มคณะใหม่</h4>
            
            <form method="POST" action="{{ route('student.faculty.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อคณะ</label>
                    {{-- ✅ ช่อง input ต้องชื่อ name ให้ตรงกับที่ระบุใน Controller ครับ --}}
                    <input type="text" name="name" class="form-control" required placeholder="เช่น คณะวิศวกรรมศาสตร์และเทคโนโลยี">
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('student.view') }}" class="btn btn-secondary rounded-pill px-4">ย้อนกลับ</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
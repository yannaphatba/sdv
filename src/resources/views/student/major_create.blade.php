@extends('layouts.app')
@section('title', 'เพิ่มสาขาใหม่')

@section('content')
<div class="container mt-4 d-flex justify-content-center">
    <div class="card shadow-sm" style="max-width: 500px; width: 100%;">
        <div class="card-body p-4">
            <h4 class="mb-4 text-center fw-bold text-primary">เพิ่มสาขาใหม่</h4>

            <form method="POST" action="{{ route('student.major.store') }}">
                @csrf
                {{-- ✅ ตัดส่วนเลือกคณะออกแล้ว เหลือแค่ชื่อสาขา --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อสาขา</label>
                    <input type="text" name="name" class="form-control" required placeholder="เช่น วิศวกรรมคอมพิวเตอร์">
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
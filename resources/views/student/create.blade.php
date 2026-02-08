@extends('layouts.app')
@section('title','ข้อมูลนักศึกษา')
@section('content')
<div class="container mt-4 d-flex justify-content-center">

  <div class="card shadow-sm" style="max-width: 800px; width: 100%;">
    <div class="card-body">
      <h3 class="mb-4 text-center">ข้อมูลนักศึกษา</h3>

      {{-- แสดงข้อความสำเร็จ --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      {{-- แสดง error --}}
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="studentForm" method="POST" action="{{ route('student.save') }}" enctype="multipart/form-data">
        @csrf

        {{-- ================= ข้อมูลนักศึกษา ================= --}}
        @php $first = $students->first(); @endphp

        {{-- คำนำหน้า --}}
        <div class="mb-3">
          <label class="form-label">คำนำหน้า</label>
          <select name="prefix" class="form-select" disabled>
            @php $p = old('prefix', $first?->prefix ?? ''); @endphp
            <option value="">เลือก</option>
            <option value="นาย" {{ $p=='นาย'?'selected':'' }}>นาย</option>
            <option value="นาง" {{ $p=='นาง'?'selected':'' }}>นาง</option>
            <option value="นางสาว" {{ $p=='นางสาว'?'selected':'' }}>นางสาว</option>
          </select>
        </div>

        {{-- ชื่อ --}}
        <div class="mb-3">
          <label class="form-label">ชื่อ</label>
          <input type="text" name="first_name" class="form-control"
            value="{{ old('first_name', $first?->first_name ?? '') }}" readonly>
        </div>

        {{-- นามสกุล --}}
        <div class="mb-3">
          <label class="form-label">นามสกุล</label>
          <input type="text" name="last_name" class="form-control"
            value="{{ old('last_name', $first?->last_name ?? '') }}" readonly>
        </div>

        {{-- รหัสนักศึกษา --}}
        <div class="mb-3">
          <label class="form-label">รหัสนักศึกษา</label>
          <input type="text" name="student_id" class="form-control"
            value="{{ old('student_id', $first?->student_id ?? '') }}" readonly>
        </div>

        {{-- ห้อง/เตียง --}}
        <div class="mb-3">
          <label class="form-label">ห้อง/เตียง</label>
          <input type="text" name="room_bed" class="form-control"
            value="{{ old('room_bed', $first?->room_bed ?? '') }}" readonly>
        </div>

        {{-- เบอร์โทร --}}
        <div class="mb-3">
          <label class="form-label">เบอร์โทรศัพท์</label>
          <input type="text" name="phone" class="form-control"
            value="{{ old('phone', $first?->phone ?? '') }}" readonly>
        </div>

        {{-- อัปโหลดรูปโปรไฟล์ --}}
        <div class="mb-3">
          <label class="form-label">อัปโหลดรูปโปรไฟล์</label>
          <input type="file" name="profile_image" class="form-control" accept="image/*">
          @if(!empty($first?->profile_image))
            <img src="{{ asset('storage/'.$first->profile_image) }}" alt="Profile"
                 width="120" class="mt-2 rounded shadow">
          @endif
        </div>

        {{-- ================= ยานพาหนะ ================= --}}
        <h5 class="mt-4">เพิ่ม/แก้ไขยานพาหนะ</h5>
        <div class="vehicle-form border rounded p-3 mb-3 bg-light">
          <div class="mb-3">
            <label class="form-label">ชนิดพาหนะ</label>
            <input type="text" name="vehicle_type[]" class="form-control" placeholder="ชนิดพาหนะ" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">หมายเลขทะเบียนรถ</label>
            <div class="row g-2">
              <div class="col-md-3">
                <input type="text" name="license_alpha[]" class="form-control" placeholder="อักษร (กข)" readonly>
              </div>
              <div class="col-md-3">
                <input type="text" name="license_number[]" class="form-control" placeholder="เลข (1234)" readonly>
              </div>
              <div class="col-md-6">
                <input type="text" name="license_province[]" class="form-control" placeholder="จังหวัด" readonly>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">ยี่ห้อ</label>
            <input type="text" name="brand[]" class="form-control" placeholder="ยี่ห้อ" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">รุ่น</label>
            <input type="text" name="model[]" class="form-control" placeholder="รุ่น" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">สีรถ</label>
            <input type="text" name="color[]" class="form-control" placeholder="สี" readonly>
          </div>

          {{-- อัปโหลดรูปรถ --}}
          <div class="mb-3">
            <label class="form-label">อัปโหลดรูปรถ</label>
            <input type="file" name="vehicle_image[]" class="form-control" accept="image/*">
            @if(!empty($first?->vehicle_image))
              <img src="{{ asset('storage/'.$first->vehicle_image) }}" alt="Vehicle"
                   width="120" class="mt-2 rounded shadow">
            @endif
          </div>
        </div>

        {{-- ปุ่ม --}}
        <div class="text-center mt-4 d-flex justify-content-center gap-2">
          <button type="button" id="editBtn" class="btn btn-warning">แก้ไข</button>
          <button type="button" id="cancelBtn" class="btn btn-secondary d-none">ยกเลิก</button>
          <button type="submit" id="saveBtn" class="btn btn-success d-none">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

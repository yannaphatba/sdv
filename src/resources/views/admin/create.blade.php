@extends('layouts.app')
@section('title','ข้อมูลนักศึกษา')
@section('content')
<div class="container mt-4 d-flex justify-content-center">

  <div class="card shadow-sm" style="max-width: 800px; width: 100%;">
    <div class="card-body">
      <h3 class="mb-4 text-center">เพิ่มข้อมูลนักศึกษาใหม่</h3>

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

      <form id="studentForm" method="POST" action="{{ route('admin.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- ================= ข้อมูลนักศึกษา ================= --}}
        
        {{-- คำนำหน้า --}}
        <div class="mb-3">
          <label class="form-label">คำนำหน้า</label>
          <select name="prefix" class="form-select">
            <option value="">เลือก</option>
            <option value="นาย" {{ old('prefix')=='นาย'?'selected':'' }}>นาย</option>
            <option value="นาง" {{ old('prefix')=='นาง'?'selected':'' }}>นาง</option>
            <option value="นางสาว" {{ old('prefix')=='นางสาว'?'selected':'' }}>นางสาว</option>
          </select>
        </div>

        {{-- ชื่อ --}}
        <div class="mb-3">
          <label class="form-label">ชื่อ</label>
          <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
        </div>

        {{-- นามสกุล --}}
        <div class="mb-3">
          <label class="form-label">นามสกุล</label>
          <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
        </div>

        {{-- รหัสนักศึกษา --}}
        <div class="mb-3">
          <label class="form-label">รหัสนักศึกษา</label>
          <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}" required>
        </div>

        {{-- ห้อง/เตียง --}}
        <div class="mb-3">
          <label class="form-label">ห้อง/เตียง</label>
          <input type="text" name="room_bed" class="form-control" value="{{ old('room_bed') }}">
        </div>

        {{-- เบอร์โทร --}}
        <div class="mb-3">
          <label class="form-label">เบอร์โทรศัพท์</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>

        {{-- ช่องกรอกสติ๊กเกอร์ (เพิ่มใหม่ตรงนี้) --}}
        <div class="card bg-warning bg-opacity-10 border-warning mb-4 mt-3">
            <div class="card-body">
                <label class="form-label fw-bold text-dark">🏷️ หมายเลขสติ๊กเกอร์ (ถ้ามี)</label>
                <input type="text" name="sticker_number" class="form-control fw-bold fs-5 text-primary"
                       placeholder="ระบุเลขสติ๊กเกอร์"
                       value="{{ old('sticker_number') }}">
                <div class="form-text">เลขนี้จะใช้เป็น QR Code และใช้สำหรับรถทุกคันของนักศึกษาคนนี้</div>
            </div>
        </div>

        {{-- คณะ --}}
        <div class="mb-3">
            <label class="form-label">คณะ</label>
            <select name="faculty_id" class="form-select">
                <option value="">-- เลือกคณะ --</option>
                @foreach($faculties as $f)
                    <option value="{{ $f->id }}" {{ old('faculty_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- สาขา --}}
        <div class="mb-3">
            <label class="form-label">สาขา</label>
            <select name="major_id" class="form-select">
                <option value="">-- เลือกสาขา --</option>
                @foreach($majors as $m)
                    <option value="{{ $m->id }}" {{ old('major_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- อาจารย์ที่ปรึกษา --}}
        <div class="mb-3">
            <label class="form-label">อาจารย์ที่ปรึกษา</label>
            <select name="advisor_id" class="form-select">
                <option value="">-- เลือกอาจารย์ --</option>
                @foreach($advisors as $advisor)
                    <option value="{{ $advisor->id }}" {{ old('advisor_id') == $advisor->id ? 'selected' : '' }}>
                        {{ $advisor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- อัปโหลดรูปโปรไฟล์ --}}
        <div class="mb-3">
          <label class="form-label">อัปโหลดรูปโปรไฟล์</label>
          <input type="file" name="profile_image" class="form-control" accept="image/*">
        </div>

        {{-- ================= ยานพาหนะ (Javascript Add) ================= --}}
        <h5 class="mt-4">เพิ่มยานพาหนะ</h5>
        <div id="new-vehicles">
             {{-- เริ่มต้นมีให้ 1 คัน --}}
             <div class="vehicle-form border rounded p-3 mb-3 bg-light">
                <h6 class="fw-bold text-primary">🚗 ยานพาหนะคันที่ 1</h6>
                <div class="mb-3">
                  <label class="form-label">ชนิดพาหนะ</label>
                  <select name="vehicle_type[]" class="form-select">
                    <option value="รถจักรยานยนต์">รถจักรยานยนต์</option>
                    <option value="รถยนต์">รถยนต์</option>
                    <option value="รถจักรยาน">รถจักรยาน</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">ทะเบียนรถ</label>
                  <div class="row g-2">
                    <div class="col-md-3">
                      <input type="text" name="license_alpha[]" class="form-control" placeholder="อักษร">
                    </div>
                    <div class="col-md-3">
                      <input type="text" name="license_number[]" class="form-control" placeholder="เลข">
                    </div>
                    <div class="col-md-6">
                      <input type="text" name="license_province[]" class="form-control" placeholder="จังหวัด">
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">ยี่ห้อ</label>
                  <input type="text" name="brand[]" class="form-control" placeholder="ยี่ห้อ">
                </div>
                <div class="mb-3">
                  <label class="form-label">รุ่น</label>
                  <input type="text" name="model[]" class="form-control" placeholder="รุ่น">
                </div>
                <div class="mb-3">
                  <label class="form-label">สีรถ</label>
                  <input type="text" name="color[]" class="form-control" placeholder="สีรถ">
                </div>
                <div class="mb-3">
                  <label class="form-label">รูปรถ</label>
                  <input type="file" name="vehicle_image[]" class="form-control" accept="image/*">
                </div>
             </div>
        </div>

        <div class="text-end mb-3">
            <button type="button" id="addVehicleBtn" class="btn btn-outline-primary btn-sm">
              + เพิ่มรถอีกคัน
            </button>
        </div>

        {{-- ปุ่ม --}}
        <div class="text-center mt-4">
          <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary me-2">ยกเลิก</a>
          <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
  const addVehicleBtn = document.getElementById("addVehicleBtn");
  const newVehicles   = document.getElementById("new-vehicles");
  let count = 1;

  addVehicleBtn?.addEventListener("click", () => {
    count++;
    const template = `
      <div class="vehicle-form border rounded p-3 mb-3 bg-light">
        <div class="d-flex justify-content-between">
            <h6 class="fw-bold text-primary">🚗 ยานพาหนะคันที่ ${count}</h6>
            <button type="button" class="btn btn-danger btn-sm remove-vehicle">ลบ</button>
        </div>
        <div class="mb-3">
          <label class="form-label">ชนิดพาหนะ</label>
          <select name="vehicle_type[]" class="form-select">
            <option value="รถจักรยานยนต์">รถจักรยานยนต์</option>
            <option value="รถยนต์">รถยนต์</option>
            <option value="รถจักรยาน">รถจักรยาน</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">ทะเบียนรถ</label>
          <div class="row g-2">
            <div class="col-md-3">
              <input type="text" name="license_alpha[]" class="form-control" placeholder="อักษร">
            </div>
            <div class="col-md-3">
              <input type="text" name="license_number[]" class="form-control" placeholder="เลข">
            </div>
            <div class="col-md-6">
              <input type="text" name="license_province[]" class="form-control" placeholder="จังหวัด">
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">ยี่ห้อ</label>
          <input type="text" name="brand[]" class="form-control" placeholder="ยี่ห้อ">
        </div>
        <div class="mb-3">
          <label class="form-label">รุ่น</label>
          <input type="text" name="model[]" class="form-control" placeholder="รุ่น">
        </div>
        <div class="mb-3">
          <label class="form-label">สีรถ</label>
          <input type="text" name="color[]" class="form-control" placeholder="สีรถ">
        </div>
        <div class="mb-3">
          <label class="form-label">รูปรถ</label>
          <input type="file" name="vehicle_image[]" class="form-control" accept="image/*">
        </div>
      </div>
    `;
    newVehicles.insertAdjacentHTML("beforeend", template);
  });

  // ลบฟอร์มรถที่เพิ่มมา
  newVehicles.addEventListener("click", (e) => {
      if(e.target.classList.contains("remove-vehicle")){
          e.target.closest(".vehicle-form").remove();
      }
  });
});
</script>
@endpush
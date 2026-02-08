@extends('layouts.app')
@section('title','แก้ไขข้อมูลนักศึกษา')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">

            <div class="card shadow rounded-4 overflow-hidden border-0">

                {{-- Header สีพื้นหลัง --}}
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลนักศึกษา</h4>
                    <small class="opacity-75">รหัส: {{ $student->student_id }}</small>
                </div>

                <div class="card-body p-4">

                    {{-- Alert Messages --}}
                    @if ($errors->any())
                    <div class="alert alert-danger shadow-sm rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>บันทึกไม่สำเร็จ</strong>
                        <ul class="mb-0 mt-1 small ms-3">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.update', $student->id) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        {{-- ================= 1. ส่วนข้อมูลส่วนตัว ================= --}}

                        {{-- การ์ดเลขสติ๊กเกอร์ --}}
                        <div class="card bg-warning bg-opacity-10 border border-warning border-opacity-50 shadow-sm mb-4 rounded-3">
                            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center text-center text-md-start">
                                <div class="mb-2 mb-md-0">
                                    <h5 class="mb-1 fw-bold text-dark"><i class="bi bi-tag-fill text-warning me-2"></i>หมายเลขสติ๊กเกอร์</h5>
                                    <small class="text-muted">เลขนี้จะใช้สร้าง QR Code ประจำตัว</small>
                                </div>
                                <div class="position-relative">
                                    <input type="number"
                                        name="sticker_number"
                                        class="form-control form-control-lg fw-bold text-primary text-center border-warning shadow-sm"
                                        style="width: 150px; font-size: 1.5rem;"
                                        value="{{ old('sticker_number', $student->sticker_number) }}"
                                        inputmode="numeric"
                                        oninput="if(value.length>4)value=value.slice(0,4); this.value = this.value.replace(/[^0-9]/g, '');"
                                        placeholder="----">
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">ข้อมูลส่วนตัว</h5>

                        {{-- คำนำหน้า --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small">คำนำหน้า</label>
                            <select name="prefix" class="form-select bg-light">
                                <option value="นาย" {{ $student->prefix=='นาย'?'selected':'' }}>นาย</option>
                                <option value="นาง" {{ $student->prefix=='นาง'?'selected':'' }}>นาง</option>
                                <option value="นางสาว" {{ $student->prefix=='นางสาว'?'selected':'' }}>นางสาว</option>
                            </select>
                        </div>

                        {{-- ชื่อ - นามสกุล --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small">ชื่อ</label>
                                <input type="text" name="first_name" class="form-control bg-light" value="{{ old('first_name', $student->first_name) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">นามสกุล</label>
                                <input type="text" name="last_name" class="form-control bg-light" value="{{ old('last_name', $student->last_name) }}">
                            </div>
                        </div>

                        {{-- รหัสนักศึกษา --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small">รหัสนักศึกษา</label>
                            <input type="text" name="student_id" class="form-control bg-light" value="{{ old('student_id', $student->student_id) }}">
                        </div>

                        {{-- ห้อง - เบอร์โทร --}}
                        <div class="row g-2 mb-3">
                            <div class="col-5">
                                <label class="form-label text-muted small">ห้อง/เตียง</label>
                                <input type="text" name="room_bed" class="form-control bg-light text-center" value="{{ old('room_bed', $student->room_bed) }}">
                            </div>
                            <div class="col-7">
                                <label class="form-label text-muted small">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone" class="form-control bg-light" value="{{ old('phone', $student->phone) }}">
                            </div>
                        </div>

                        {{-- ================= 2. ข้อมูลการศึกษา ================= --}}
                        <h5 class="fw-bold text-primary mt-4 mb-3 border-bottom pb-2">ข้อมูลการศึกษา</h5>

                        <div class="mb-3">
                            <label class="form-label text-muted small">คณะ</label>
                            <select name="faculty_id" class="form-select bg-light">
                                <option value="">-- เลือกคณะ --</option>
                                @foreach($faculties as $f)
                                <option value="{{ $f->id }}" {{ $student->faculty_id == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small">สาขา</label>
                            <select name="major_id" class="form-select bg-light">
                                <option value="">-- เลือกสาขา --</option>
                                @foreach($majors as $m)
                                <option value="{{ $m->id }}" {{ $student->major_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small">อาจารย์ที่ปรึกษา</label>
                            <select name="advisor_id" class="form-select bg-light">
                                <option value="">-- เลือกอาจารย์ --</option>
                                @foreach($advisors as $adv)
                                <option value="{{ $adv->id }}" {{ $student->advisor_id == $adv->id ? 'selected' : '' }}>
                                    {{ $adv->name }} ({{ $adv->phone }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- อัปโหลดรูป --}}
                        <div class="mt-4 p-3 border rounded bg-light">
                            <label class="form-label fw-bold">รูปโปรไฟล์</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if(!empty($student->profile_image))
                                    <img src="{{ asset('storage/'.$student->profile_image) }}" width="60" height="60" class="rounded-circle shadow-sm object-fit-cover">
                                    @else
                                    <div class="bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="bi bi-person-fill text-secondary fs-3"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>

                        {{-- ================= 3. ยานพาหนะ (แก้ไขได้) ================= --}}
                        <h5 class="fw-bold text-primary mt-5 mb-3 border-bottom pb-2">ยานพาหนะ (แก้ไข/ลบ)</h5>

                        <div id="vehicle-wrapper">
                            @foreach($vehicles as $i => $v)
                            <div class="vehicle-form card mb-3 border shadow-sm position-relative">
                                {{-- ปุ่มลบรถ (เฉพาะแอดมิน) --}}
                                <button type="submit" form="delete-vehicle-{{ $v->id }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 shadow-sm" style="z-index: 5;">
                                    <i class="bi bi-trash-fill"></i> ลบ
                                </button>

                                <div class="card-header bg-white py-2">
                                    <span class="fw-bold text-primary"><i class="bi bi-car-front-fill me-2"></i>คันที่ {{ $i+1 }} ({{ $v->vehicle_type }})</span>
                                </div>

                                <div class="card-body bg-light">
                                    <input type="hidden" name="vehicle_ids[]" value="{{ $v->id }}">

                                    {{-- ประเภท --}}
                                    <div class="mb-2">
                                        <label class="form-label text-muted small mb-1">ประเภท</label>
                                        <select name="vehicle_type_existing[]" class="form-select form-select-sm">
                                            <option value="รถจักรยานยนต์" {{ $v->vehicle_type=='รถจักรยานยนต์'?'selected':'' }}>รถจักรยานยนต์</option>
                                            <option value="รถยนต์" {{ $v->vehicle_type=='รถยนต์'?'selected':'' }}>รถยนต์</option>
                                            <option value="รถจักรยาน" {{ $v->vehicle_type=='รถจักรยาน'?'selected':'' }}>รถจักรยาน</option>
                                        </select>
                                    </div>

                                    {{-- ทะเบียน --}}
                                    <div class="mb-2">
                                        <label class="form-label text-muted small mb-1">ทะเบียนรถ</label>
                                        <div class="row g-2">
                                            <div class="col-3">
                                                <input type="text" name="license_alpha_existing[]" class="form-control form-control-sm text-center" value="{{ $v->license_alpha }}" placeholder="กข">
                                            </div>
                                            <div class="col-3">
                                                <input type="text" name="license_number_existing[]" class="form-control form-control-sm text-center" value="{{ $v->license_number }}" placeholder="1234">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="license_province_existing[]" class="form-control form-control-sm" value="{{ $v->license_province }}" placeholder="จังหวัด">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ยี่ห้อ รุ่น สี --}}
                                    {{-- ✅ บรรทัดนี้คือส่วนที่ริวต้องเช็คครับ --}}
                                    <div class="row g-2 mb-2">
                                        <div class="col-4">
                                            <label class="form-label text-muted small mb-1">ยี่ห้อ</label>
                                            <input type="text" name="brand_existing[]" class="form-control form-control-sm" value="{{ $v->brand }}">
                                        </div>

                                        <div class="col-4">
                                            <label class="form-label text-muted small mb-1">รุ่น</label>
                                            {{-- 🛠 แก้/ตรวจสอบชื่อ name ตรงนี้ให้เป็น model_existing[] ครับ ริว --}}
                                            <input type="text" name="model_existing[]" class="form-control form-control-sm" value="{{ $v->model }}">
                                        </div>

                                        <div class="col-4">
                                            <label class="form-label text-muted small mb-1">สี</label>
                                            <input type="text" name="color_existing[]" class="form-control form-control-sm" value="{{ $v->color }}">
                                        </div>
                                    </div>

                                    {{-- รูปรถ --}}
                                    <div class="mt-2 pt-2 border-top">
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!empty($v->vehicle_image))
                                            <img src="{{ asset('storage/'.$v->vehicle_image) }}" width="40" class="rounded border">
                                            @endif
                                            <input type="file" name="vehicle_image_existing[{{ $i }}]" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- ส่วนเพิ่มรถใหม่ --}}
                        <div id="new-vehicles"></div>

                        <div class="text-center my-4">
                            <button type="button" id="addVehicleBtn" class="btn btn-outline-success border-2 rounded-pill fw-bold px-4 hover-scale">
                                <i class="bi bi-plus-circle-fill me-2"></i>เพิ่มรถคันใหม่
                            </button>
                        </div>

                        <hr class="my-4">

                        {{-- ปุ่มบันทึก --}}
                        <div class="row g-3">
                            <div class="col-6">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg rounded-pill w-100 shadow-sm">
                                    ยกเลิก
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill w-100 shadow fw-bold">
                                    บันทึกการแก้ไข
                                </button>
                            </div>
                        </div>

                    </form>

                    {{-- Form ลบรถ (ซ่อนไว้) --}}
                    @foreach($vehicles as $v)
                    <form id="delete-vehicle-{{ $v->id }}" action="{{ route('admin.vehicle.delete', $v->id) }}" method="POST" onsubmit="return confirm('ยืนยันลบรถคันนี้?')" style="display:none;">
                        @csrf @method('DELETE')
                    </form>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }

    .hover-scale:hover {
        transform: translateY(-2px);
    }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const addVehicleBtn = document.getElementById("addVehicleBtn");
        const newVehicles = document.getElementById("new-vehicles");

        // ฟังก์ชันเพิ่มรถใหม่ 
        addVehicleBtn?.addEventListener("click", () => {
            const template = `
        <div class="vehicle-form card mb-3 border border-success shadow-sm animate__animated animate__fadeIn">
            <div class="card-header bg-success text-white py-1 d-flex justify-content-between align-items-center">
                <span class="fw-bold small"><i class="bi bi-plus-circle me-1"></i>รถใหม่</span>
                <button type="button" class="btn-close btn-close-white remove-new-vehicle" style="font-size: 0.7rem;"></button>
            </div>
            <div class="card-body bg-white p-3">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small mb-0">ประเภท</label>
                        <select name="vehicle_type[]" class="form-select form-select-sm">
                            <option value="รถจักรยานยนต์">รถจักรยานยนต์</option>
                            <option value="รถยนต์">รถยนต์</option>
                            <option value="รถจักรยาน">รถจักรยาน</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-8">
                        <label class="form-label text-muted small mb-0">ทะเบียน</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="license_alpha[]" class="form-control text-center" placeholder="กข">
                            <input type="text" name="license_number[]" class="form-control text-center" placeholder="1234">
                            <input type="text" name="license_province[]" class="form-control" placeholder="จังหวัด">
                        </div>
                    </div>
                    <div class="col-4">
                        <input type="text" name="brand[]" class="form-control form-control-sm" placeholder="ยี่ห้อ">
                    </div>
                    <div class="col-4">
                        <input type="text" name="model[]" class="form-control form-control-sm" placeholder="รุ่น">
                    </div>
                    <div class="col-4">
                        <input type="text" name="color[]" class="form-control form-control-sm" placeholder="สี">
                    </div>
                    <div class="col-12 mt-2">
                        <input type="file" name="vehicle_image[]" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>
            </div>
        </div>
        `;
            newVehicles.insertAdjacentHTML("beforeend", template);
        });

        // ลบรถใหม่ที่เพิ่งกดเพิ่ม
        newVehicles.addEventListener("click", function(e) {
            if (e.target.classList.contains("remove-new-vehicle")) {
                e.target.closest(".vehicle-form").remove();
            }
        });
    });
</script>
@endpush
@extends('layouts.app')
@section('title','ข้อมูลนักศึกษา')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">

            <div class="card shadow rounded-4 overflow-hidden border-0">

                {{-- Header สีพื้นหลัง --}}
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-person-vcard me-2"></i>ข้อมูลนักศึกษา</h4>
                </div>

                <div class="card-body p-4">

                    {{-- Alert Messages --}}
                    @if(session('success'))
                    <div class="alert alert-success shadow-sm rounded-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger shadow-sm rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                    <div class="alert alert-danger shadow-sm rounded-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- ================= FORM ================= --}}
                    <form id="studentForm" method="POST" action="{{ route('student.update', $student->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- ================= 1. ส่วนข้อมูลส่วนตัว ================= --}}

                        {{-- แสดงเลขสติ๊กเกอร์ (ปรับให้มือถือเรียงลง ไอแพดเรียงนอน) --}}
                        <div class="card bg-info bg-opacity-10 border border-info border-opacity-25 shadow-sm mb-4 rounded-3">
                            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center text-center text-md-start">
                                <div class="mb-2 mb-md-0">
                                    <h5 class="mb-1 fw-bold text-dark"><i class="bi bi-tags-fill text-primary me-2"></i>หมายเลขสติ๊กเกอร์</h5>
                                    <small class="text-muted">ใช้สำหรับยืนยันตัวตนกับยานพาหนะทุกคัน</small>
                                </div>
                                <div class="badge bg-white text-primary fs-3 fw-bold border border-primary shadow-sm px-4 py-2 rounded-pill">
                                    {{ $student->sticker_number ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">ข้อมูลส่วนตัว</h5>

                        {{-- คำนำหน้า --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small">คำนำหน้า</label>
                            <select name="prefix" class="form-select lockable bg-light" disabled>
                                <option value="">เลือก</option>
                                <option value="นาย" {{ $student->prefix=='นาย'?'selected':'' }}>นาย</option>
                                <option value="นาง" {{ $student->prefix=='นาง'?'selected':'' }}>นาง</option>
                                <option value="นางสาว" {{ $student->prefix=='นางสาว'?'selected':'' }}>นางสาว</option>
                            </select>
                        </div>

                        {{-- ชื่อ - นามสกุล (อยู่คู่กัน) --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small">ชื่อ</label>
                                <input type="text" name="first_name" class="form-control lockable bg-light" value="{{ $student->first_name }}" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small">นามสกุล</label>
                                <input type="text" name="last_name" class="form-control lockable bg-light" value="{{ $student->last_name }}" readonly>
                            </div>
                        </div>

                        {{-- รหัสนักศึกษา --}}
                        <div class="mb-3">
                            <label class="form-label text-muted small">รหัสนักศึกษา</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="student_id" class="form-control lockable bg-light border-start-0 ps-0" value="{{ $student->student_id }}" readonly>
                            </div>
                        </div>

                        {{-- ห้อง - เบอร์โทร (อยู่คู่กัน) --}}
                        <div class="row g-2 mb-3">
                            <div class="col-5 col-md-4">
                                <label class="form-label text-muted small">ห้อง/เตียง</label>
                                <input type="text" name="room_bed" class="form-control lockable bg-light text-center" value="{{ $student->room_bed }}" readonly>
                            </div>
                            <div class="col-7 col-md-8">
                                <label class="form-label text-muted small">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone" class="form-control lockable bg-light" value="{{ $student->phone }}" readonly>
                            </div>
                        </div>

                        {{-- ================= 2. ข้อมูลการศึกษา (Read Only) ================= --}}
                        <h5 class="fw-bold text-primary mt-4 mb-3 border-bottom pb-2">ข้อมูลการศึกษา</h5>

                        {{-- 💡 เปลี่ยนคำอธิบายใหม่ --}}
                        <div class="alert alert-info border border-info border-opacity-25 py-2 d-flex align-items-center" role="alert">
                            <i class="bi bi-pencil-fill text-primary me-2"></i> <small>กรุณาตรวจสอบและเลือกข้อมูลการศึกษาของคุณให้ถูกต้อง</small>
                        </div>

                        <div class="row g-3">
                            {{-- 1. ส่วนคณะ --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small">คณะ</label>
                                <select name="faculty_id" class="form-select lockable bg-light" disabled>
                                    <option value="">-- เลือกคณะ --</option>
                                    @foreach($faculties as $f)
                                    <option value="{{ $f->id }}" {{ $student->faculty_id == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-end mt-1">
                                    {{-- ✅ เติม target="_blank" เพื่อเปิดหน้าต่างใหม่ ข้อมูลเดิมไม่หาย --}}
                                    <a href="{{ route('student.faculty.create') }}" target="_blank" class="small text-decoration-none lockable d-none">+ เพิ่มคณะใหม่</a>
                                </div>
                            </div>

                            {{-- 2. ส่วนสาขา --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small">สาขา</label>
                                <select name="major_id" class="form-select lockable bg-light" disabled>
                                    <option value="">-- เลือกสาขา --</option>
                                    @foreach($majors as $m)
                                    <option value="{{ $m->id }}" {{ $student->major_id == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                                    @endforeach
                                </select>
                                <div class="text-end mt-1">
                                    {{-- ✅ เติม target="_blank" --}}
                                    <a href="{{ route('student.major.create') }}" target="_blank" class="small text-decoration-none lockable d-none">+ เพิ่มสาขาใหม่</a>
                                </div>
                            </div>

                            {{-- 3. ส่วนอาจารย์ที่ปรึกษา --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label text-muted small">อาจารย์ที่ปรึกษา</label>
                                <select name="advisor_id" class="form-select lockable bg-light" disabled>
                                    <option value="">-- เลือกอาจารย์ --</option>
                                    @foreach($advisors as $adv)
                                    <option value="{{ $adv->id }}" {{ $student->advisor_id == $adv->id ? 'selected' : '' }}>
                                        {{ $adv->name }} {{ $adv->phone ? '('.$adv->phone.')' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="text-end mt-1">
                                    {{-- ✅ เติม target="_blank" --}}
                                    <a href="{{ route('student.advisor.create') }}" target="_blank" class="small text-decoration-none lockable d-none">+ เพิ่มอาจารย์ใหม่</a>
                                </div>
                            </div>
                        </div>
                        {{-- อัปโหลดรูป --}}
                        <div class="mt-4 p-3 border rounded bg-light">
                            <label class="form-label fw-bold">รูปโปรไฟล์</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0">
                                    @if(!empty($student->profile_image))
                                    <img src="{{ asset('storage/'.$student->profile_image) }}" alt="Profile" width="60" height="60" class="rounded-circle shadow-sm object-fit-cover">
                                    @else
                                    <div class="bg-secondary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="bi bi-person-fill text-secondary fs-3"></i>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="profile_image" class="form-control lockable" accept="image/*" disabled>
                                    <small class="text-muted">รองรับไฟล์ภาพ jpg, png</small>
                                </div>
                            </div>
                        </div>

                        {{-- ================= 3. ยานพาหนะ ================= --}}
                        <h5 class="fw-bold text-primary mt-5 mb-3 border-bottom pb-2">ยานพาหนะของฉัน</h5>

                        <div class="alert alert-warning border-warning d-flex align-items-start shadow-sm rounded-3">
                            <i class="bi bi-exclamation-circle-fill fs-5 me-2 mt-1"></i>
                            <div>
                                <strong>ข้อควรระวัง:</strong> รถที่ลงทะเบียนแล้ว <u>ไม่สามารถแก้ไขเองได้</u><br>
                                <small>หากต้องการแก้ไข/ลบ กรุณาติดต่อ Admin หรือ รปภ.</small>
                            </div>
                        </div>

                        <div id="vehicle-wrapper">
                            @foreach($vehicles as $i => $v)
                            <div class="vehicle-form card mb-3 border shadow-sm">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                                    <span class="fw-bold text-primary"><i class="bi bi-car-front-fill me-2"></i>คันที่ {{ $i+1 }}</span>
                                    <span class="badge bg-secondary"><i class="bi bi-lock-fill me-1"></i>ล็อค</span>
                                </div>
                                <div class="card-body bg-light">
                                    <input type="hidden" name="vehicle_ids[]" value="{{ $v->id }}">

                                    {{-- ประเภทรถ --}}
                                    <div class="mb-2">
                                        <label class="form-label text-muted small mb-1">ประเภท</label>
                                        <input type="text" class="form-control bg-white form-control-sm" value="{{ $v->vehicle_type }}" readonly disabled>
                                    </div>

                                    {{-- ทะเบียนรถ (จัด Grid ใหม่: อักษร-เลข คู่กัน / จังหวัดลงมาล่าง) --}}
                                    <div class="mb-2">
                                        <label class="form-label text-muted small mb-1">ทะเบียนรถ</label>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <input type="text" class="form-control bg-white form-control-sm text-center" value="{{ $v->license_alpha }}" readonly disabled>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <input type="text" class="form-control bg-white form-control-sm text-center" value="{{ $v->license_number }}" readonly disabled>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <input type="text" class="form-control bg-white form-control-sm" value="{{ $v->license_province }}" readonly disabled>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ยี่ห้อ รุ่น สี (จัดกลุ่ม) --}}
                                    <div class="row g-2 mb-2">
                                        <div class="col-6 col-md-4">
                                            <label class="form-label text-muted small mb-1">ยี่ห้อ</label>
                                            <input type="text" class="form-control bg-white form-control-sm" value="{{ $v->brand }}" readonly disabled>
                                        </div>
                                        <div class="col-6 col-md-4">
                                            <label class="form-label text-muted small mb-1">รุ่น</label>
                                            <input type="text" class="form-control bg-white form-control-sm" value="{{ $v->model }}" readonly disabled>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label text-muted small mb-1">สี</label>
                                            <input type="text" class="form-control bg-white form-control-sm" value="{{ $v->color }}" readonly disabled>
                                        </div>
                                    </div>

                                    @if($v->vehicle_image)
                                    <div class="mt-2 text-center text-md-start">
                                        <img src="{{ asset('storage/'.$v->vehicle_image) }}" class="rounded shadow-sm border" style="max-height: 80px; width: auto;">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- ส่วนเพิ่มรถใหม่ --}}
                        <div id="new-vehicles"></div>

                        <div class="text-center my-4">
                            <button type="button" id="addVehicleBtn" class="btn btn-outline-primary border-2 rounded-pill fw-bold lockable px-4 hover-scale" disabled>
                                <i class="bi bi-plus-circle-fill me-2"></i>ลงทะเบียนรถคันใหม่
                            </button>
                        </div>

                        {{-- ปุ่มจัดการ --}}
                        <hr class="my-4">
                        <div class="d-grid gap-2 d-md-block text-center">
                            <button type="button" id="editBtn" class="btn btn-warning btn-lg shadow-sm px-5 rounded-pill fw-bold text-dark">
                                <i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูล / เพิ่มรถ
                            </button>

                            <div class="d-inline-flex gap-2 justify-content-center w-100 w-md-auto">
                                <button type="button" id="cancelBtn" class="btn btn-secondary btn-lg rounded-pill px-4 d-none w-50 w-md-auto">ยกเลิก</button>
                                <button type="submit" id="saveBtn" class="btn btn-success btn-lg rounded-pill px-5 shadow fw-bold d-none w-50 w-md-auto">
                                    <i class="bi bi-save me-2"></i>บันทึก
                                </button>
                            </div>
                        </div>

                    </form>
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
        transform: scale(1.05);
    }

    .form-control:disabled,
    .form-control[readonly] {
        opacity: 0.8;
        cursor: not-allowed;
    }
</style>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const form = document.getElementById("studentForm");
        const editBtn = document.getElementById("editBtn");
        const saveBtn = document.getElementById("saveBtn");
        const cancelBtn = document.getElementById("cancelBtn");
        const addVehicleBtn = document.getElementById("addVehicleBtn");
        const newVehicles = document.getElementById("new-vehicles");

        /* Lock form ตอนเริ่มต้น */
        form.querySelectorAll(".lockable").forEach(el => {
            if (el.tagName === "SELECT" || el.type === "file" || el.tagName === "BUTTON") {
                el.disabled = true;
            } else {
                el.readOnly = true;
            }
        });

        /* ปลดล็อกเมื่อกดแก้ไข */
        editBtn.addEventListener("click", () => {
            form.querySelectorAll(".lockable").forEach(el => {
                el.disabled = false;
                el.readOnly = false;
                el.classList.remove("bg-light");
                el.classList.add("bg-white");

                // ✅ แทรกบรรทัดนี้ลงไปครับริว เพื่อปลดล็อกปุ่ม "เพิ่ม..."
                el.classList.remove("d-none");
            });

            editBtn.classList.add("d-none");
            saveBtn.classList.remove("d-none");
            cancelBtn.classList.remove("d-none");

            saveBtn.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        });

        cancelBtn.addEventListener("click", () => location.reload());

        /* ======================= เพิ่มรถใหม่ ======================= */
        addVehicleBtn?.addEventListener("click", () => {
            const template = `
        <div class="vehicle-form card mb-3 border border-success shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold"><i class="bi bi-plus-circle me-2"></i>รถใหม่ (กำลังเพิ่ม)</span>
                <button type="button" class="btn-close btn-close-white remove-new-vehicle" aria-label="Close"></button>
            </div>
            <div class="card-body bg-white">
                <div class="mb-3">
                    <label class="form-label small text-muted">ชนิดพาหนะ</label>
                    <select name="vehicle_type[]" class="form-select">
                        <option value="รถจักรยานยนต์">รถจักรยานยนต์</option>
                        <option value="รถยนต์">รถยนต์</option>
                        <option value="รถจักรยาน">รถจักรยาน</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small text-muted">ทะเบียนรถ</label>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <input type="text" name="license_alpha[]" class="form-control text-center" placeholder="กข">
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="text" name="license_number[]" class="form-control text-center" placeholder="1234">
                        </div>
                        <div class="col-12 col-md-6">
                            <input type="text" name="license_province[]" class="form-control" placeholder="จังหวัด (เช่น ขอนแก่น)">
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-4">
                        <label class="form-label small text-muted">ยี่ห้อ</label>
                        <input type="text" name="brand[]" class="form-control" placeholder="เช่น Honda">
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label small text-muted">รุ่น</label>
                        <input type="text" name="model[]" class="form-control" placeholder="เช่น Wave 110i">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-muted">สีรถ</label>
                        <input type="text" name="color[]" class="form-control" placeholder="เช่น แดง-ดำ">
                    </div>
                </div>

                <div>
                    <label class="form-label small text-muted">รูปรถ</label>
                    <input type="file" name="vehicle_image[]" class="form-control" accept="image/*">
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
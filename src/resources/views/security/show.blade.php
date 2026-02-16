@extends('layouts.app')
@section('title', 'รายละเอียดข้อมูลนักศึกษา')

@section('content')
<div class="container mt-4 mb-5">

    {{-- Header & Back Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0">
            <i class="bi bi-file-earmark-person me-2"></i>รายละเอียดข้อมูลนักศึกษา
        </h4>
        {{-- เช็คสิทธิ์เพื่อลิงก์กลับให้ถูกหน้า --}}
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ (Admin)
        </a>
        @else
        <a href="{{ route('security.dashboard') }}" class="btn btn-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> ย้อนกลับ (รปภ.)
        </a>
        @endif
    </div>

    {{-- 1. การ์ดข้อมูลส่วนตัว --}}
    <div class="card shadow rounded-4 border-0 overflow-hidden mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>ข้อมูลทั่วไป</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">

                {{-- ส่วนรูปภาพและสติ๊กเกอร์ --}}
                <div class="col-12 col-md-4 text-center border-bottom border-md-bottom-0 border-md-end pb-4 pb-md-0">

                    {{-- รูปโปรไฟล์ --}}
                    @if(!empty($student->profile_image) && Storage::disk('public')->exists($student->profile_image))
                    <img src="{{ asset('storage/' . $student->profile_image) }}"
                        class="rounded-circle shadow border border-4 border-white mb-3 object-fit-cover"
                        style="width: 180px; height: 180px;">
                    @else
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-inner text-secondary"
                        style="width: 180px; height: 180px;">
                        <i class="bi bi-person-fill" style="font-size: 5rem;"></i>
                    </div>
                    @endif

                    <div class="mt-2">
                        <h5 class="text-muted small mb-1">หมายเลขสติ๊กเกอร์</h5>
                        <span class="badge bg-warning text-dark fs-3 px-4 py-2 rounded-pill shadow-sm border border-warning">
                            {{ $student->sticker_number ?? '-' }}
                        </span>
                    </div>
                </div>

                {{-- ส่วนข้อมูลรายละเอียด --}}
                <div class="col-12 col-md-8 ps-md-4">
                    <h3 class="fw-bold text-dark text-center text-md-start mb-3">
                        {{ $student->prefix }}{{ $student->first_name }} {{ $student->last_name }}
                    </h3>

                    <div class="row g-3 fs-5">
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small d-block">รหัสนักศึกษา</label>
                            <span class="text-primary fw-bold fs-4">{{ $student->student_id ?? '-' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="text-muted small d-block">เบอร์โทรศัพท์</label>
                            <a href="tel:{{ $student->phone }}" class="text-dark text-decoration-none fw-bold fs-5 hover-text-primary">
                                <i class="bi bi-telephone-fill text-success me-1"></i> {{ $student->phone ?? '-' }}
                            </a>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">ห้อง/เตียง</label>
                            {{-- ✅ แก้ไขตัวแปรเพื่อให้แสดงผลข้อมูลได้ถูกต้อง --}}
                            <span class="fw-bold">{{ $student->room_bed ?? '-' }}</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">คณะ/สาขา</label>
                            <span class="d-block lh-sm">
                                {{-- ✅ แก้เป็นเรียกผ่านความสัมพันธ์ faculty และ major ครับ --}}
                                {{ $student->faculty->name ?? '-' }}<br>
                                <small class="text-muted">{{ $student->major->name ?? '-' }}</small>
                            </span>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    {{-- ข้อมูลอาจารย์ --}}
                    {{-- ✅ แก้ไขใหม่เพื่อให้ชื่อและเบอร์อยู่กลุ่มเดียวกัน --}}
                    <div class="alert alert-light border shadow-sm d-flex align-items-center gap-3 rounded-3 p-3 mb-0">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                            <i class="bi bi-person-video3 fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">อาจารย์ที่ปรึกษา</small>
                            <span class="fw-bold text-dark fs-5">{{ $student->advisor->name ?? 'ไม่ระบุ' }}</span>

                            {{-- ✅ ย้ายเบอร์โทรมาไว้ใต้ชื่อตรงนี้เลย --}}
                            @if(isset($student->advisor->phone))
                            <div class="mt-1">
                                <a href="tel:{{ $student->advisor->phone }}" class="text-danger fw-bold text-decoration-none small">
                                    <i class="bi bi-telephone me-1"></i> {{ $student->advisor->phone }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    {{-- ✅ ลบส่วนแสดงผล QR Code ออกเรียบร้อยแล้วครับริว --}}
                </div>
            </div>
        </div>
    </div>

    {{-- 2. รายการยานพาหนะ --}}
    <h4 class="mb-3 fw-bold text-dark border-start border-4 border-primary ps-3">
        ยานพาหนะที่ลงทะเบียน ({{ $student->vehicles->count() }})
    </h4>

    <div class="row g-3">
        @forelse($student->vehicles as $index => $vehicle)
        <div class="col-12 col-md-6">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden hover-shadow transition-all">
                <div class="card-header text-white d-flex justify-content-between align-items-center py-2
                        {{ ($vehicle->vehicle_type == 'รถจักรยานยนต์') ? 'bg-primary' : (($vehicle->vehicle_type == 'รถยนต์') ? 'bg-success' : 'bg-secondary') }}">

                    <span class="fw-bold">
                        @if($vehicle->vehicle_type == 'รถจักรยานยนต์') <i class="bi bi-bicycle me-1"></i>
                        @elseif($vehicle->vehicle_type == 'รถยนต์') <i class="bi bi-car-front-fill me-1"></i>
                        @else <i class="bi bi-bicycle me-1"></i> @endif
                        คันที่ {{ $index + 1 }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 border border-white border-opacity-50">
                        {{ $vehicle->vehicle_type }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="d-flex gap-3">
                        {{-- รูปรถ --}}
                        <div class="flex-shrink-0">
                            @if(!empty($vehicle->vehicle_image) && Storage::disk('public')->exists($vehicle->vehicle_image))
                            <img src="{{ asset('storage/' . $vehicle->vehicle_image) }}"
                                class="rounded shadow-sm border object-fit-cover"
                                style="width: 100px; height: 100px; cursor: pointer;"
                                onclick="window.open(this.src)">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border"
                                style="width: 100px; height: 100px;">
                                <div class="text-center">
                                    <i class="bi bi-image-slash d-block fs-4"></i>
                                    <small style="font-size: 10px;">ไม่มีรูป</small>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- ข้อมูลรถ --}}
                        <div class="flex-grow-1">
                            <label class="text-muted x-small mb-0">ทะเบียนรถ</label>
                            <h4 class="fw-bold text-dark mb-0">
                                {{ $vehicle->license_alpha }} {{ $vehicle->license_number }}
                            </h4>
                            <div class="text-muted small mb-2">{{ $vehicle->license_province }}</div>

                            <div class="d-flex flex-wrap gap-1">
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-tag-fill text-secondary"></i> {{ $vehicle->brand ?? '-' }}
                                </span>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-palette-fill text-secondary"></i> {{ $vehicle->color ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-secondary text-center rounded-3 py-4">
                <i class="bi bi-exclamation-circle fs-1 text-secondary d-block mb-2"></i>
                <span class="text-muted">ไม่พบข้อมูลยานพาหนะในระบบ</span>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    .hover-text-primary:hover {
        color: var(--bs-primary) !important;
    }

    .object-fit-cover {
        object-fit: cover;
    }

    .x-small {
        font-size: 0.75rem;
    }

    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        transform: translateY(-2px);
    }

    .transition-all {
        transition: all 0.2s ease-in-out;
    }
</style>
@endsection
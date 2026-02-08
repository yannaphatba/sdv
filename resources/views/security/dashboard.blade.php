@extends('layouts.app')
@section('title', 'แดชบอร์ด รปภ.')

@section('content')
<div class="container mb-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-primary mb-0">
                <i class="bi bi-shield-check me-2"></i>ระบบรักษาความปลอดภัย
            </h4>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
            </button>
        </form>
    </div>

    {{-- ส่วนแสดงสถิติ (Stats) --}}
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 h-100 bg-primary bg-opacity-10">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title text-muted small mb-1">รถจักรยานยนต์</h6>
                    <p class="fs-4 fw-bold text-primary mb-0">{{ $motorcycleCount ?? 0 }}</p>
                    <small class="text-muted">คัน</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 h-100 bg-success bg-opacity-10">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title text-muted small mb-1">รถยนต์</h6>
                    <p class="fs-4 fw-bold text-success mb-0">{{ $carCount ?? 0 }}</p>
                    <small class="text-muted">คัน</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 h-100 bg-warning bg-opacity-10">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title text-muted small mb-1">จักรยาน</h6>
                    <p class="fs-4 fw-bold text-warning mb-0">{{ $bicycleCount ?? 0 }}</p>
                    <small class="text-muted">คัน</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 h-100 bg-dark bg-opacity-10">
                <div class="card-body p-2 p-md-3">
                    <h6 class="card-title text-muted small mb-1">ช่องจอดรถ</h6>
                    <p class="fs-4 fw-bold text-dark mb-0">{{ $slots->total_slots ?? 0 }}</p>
                    <small class="text-muted">ช่อง</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ฟอร์มค้นหา --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-12 col-md-3">
                    <select name="type" class="form-select bg-light border-0">
                        <option value="">-- ประเภทการค้นหา --</option>
                        <option value="sticker" {{ request('type')=='sticker'?'selected':'' }}>เลขสติ๊กเกอร์</option>
                        <option value="license" {{ request('type')=='license'?'selected':'' }}>ทะเบียนรถ</option>
                        <option value="name" {{ request('type')=='name'?'selected':'' }}>ชื่อ - นามสกุล</option>
                        <option value="student_id" {{ request('type')=='student_id'?'selected':'' }}>รหัสนักศึกษา</option>
                        <option value="room" {{ request('type')=='room'?'selected':'' }}>ห้อง/เตียง</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="พิมพ์คำค้นหา..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">ค้นหา</button>
                    <a href="{{ route('security.dashboard') }}" class="btn btn-light border flex-grow-1">ดูทั้งหมด</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-ul me-2"></i>รายชื่อนักศึกษา</h5>
        </div>
        <div class="card-body p-0">

            {{-- ส่วนที่ 1: ตารางสำหรับ Desktop/iPad (แก้ไขตัวแปรห้อง/เตียง) --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 14px;">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="py-3">รหัสนักศึกษา</th>
                            <th class="py-3 text-start">ชื่อ - สกุล</th>
                            <th class="py-3 bg-warning bg-opacity-10 text-dark">เลขสติ๊กเกอร์</th>
                            <th class="py-3">ห้อง/เตียง</th>
                            <th class="py-3">ยานพาหนะ</th>
                            <th class="py-3">ทะเบียน</th>
                            <th class="py-3">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($students as $stu)
                            <tr>
                                <td class="fw-bold text-primary">{{ $stu->student_id }}</td>
                                <td class="text-start" style="white-space: nowrap;">
                                    <span class="fw-bold">{{ $stu->prefix }}{{ $stu->first_name }} {{ $stu->last_name }}</span>
                                </td>
                                <td class="fw-bold text-danger bg-warning bg-opacity-10">
                                    {{ $stu->sticker_number ?? '-' }}
                                </td>
                                {{-- ✅ แก้กลับมาใช้ room_bed เพื่อให้เลขห้องโชว์ครับ --}}
                                <td>{{ $stu->room_bed ?? '-' }}</td>
                                <td>
                                    @foreach($stu->vehicles as $v)
                                        <div class="mb-1 text-start ps-4">
                                            <i class="bi bi-circle-fill text-primary" style="font-size: 6px;"></i> {{ $v->vehicle_type }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($stu->vehicles as $v)
                                        <span class="badge bg-light text-dark border mb-1">{{ $v->license_alpha }} {{ $v->license_number }}</span><br>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('security.student.show', $stu->id) }}" class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm border-0">
                                        <i class="bi bi-eye-fill"></i> ดู
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5">ไม่พบข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ส่วนที่ 2: การ์ดสำหรับมือถือ Mobile (แก้ไขตัวแปรห้อง/เตียง) --}}
            <div class="d-block d-md-none bg-light p-2">
                @forelse($students as $stu)
                    <div class="card mb-3 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div style="overflow: hidden;">
                                    <h6 class="fw-bold mb-0 text-dark" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $stu->prefix }}{{ $stu->first_name }} {{ $stu->last_name }}
                                    </h6>
                                    <small class="text-muted">รหัส: <span class="text-primary">{{ $stu->student_id }}</span></small>
                                </div>
                                <span class="badge bg-warning text-dark border border-warning">
                                    {{ $stu->sticker_number ?? 'ไม่มี' }}
                                </span>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded border text-center">
                                        <small class="text-muted d-block" style="font-size: 10px;">ห้อง/เตียง</small>
                                        {{-- ✅ แก้ไขตัวแปรสำหรับมือถือด้วยครับ --}}
                                        <span class="fw-bold">{{ $stu->room_bed ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light p-2 rounded border text-center">
                                        <small class="text-muted d-block" style="font-size: 10px;">ยานพาหนะ</small>
                                        <span class="fw-bold">{{ $stu->vehicles->count() }} คัน</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('security.student.show', $stu->id) }}" class="btn btn-info text-white w-100 fw-bold rounded-pill shadow-sm border-0">
                                <i class="bi bi-eye-fill me-1"></i> ดูรายละเอียด
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-1 d-block mb-2"></i> ไม่พบข้อมูล
                    </div>
                @endforelse
            </div>

        </div>
@endsection
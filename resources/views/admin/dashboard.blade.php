@extends('layouts.app')
@section('title','แดชบอร์ดแอดมิน')

@section('content')
<div class="container-fluid px-2 px-md-4 mt-3 mb-5">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-2 text-center text-md-start">
        <div>
            <h4 class="fw-bold text-primary mb-0">ระบบจัดการข้อมูลนักศึกษา</h4>
            <small class="text-muted">ผู้ดูแลระบบ (Admin) | มทร.อีสาน</small>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                ออกจากระบบ
            </button>
        </form>
    </div>

    {{-- 1. ส่วนแสดงสถิติ --}}
    <div class="row g-2 mb-3">
        @php
            $stats = [
                ['label' => 'รถจักรยานยนต์', 'count' => $motorcycleCount, 'color' => 'primary'],
                ['label' => 'รถยนต์', 'count' => $carCount, 'color' => 'success'],
                ['label' => 'จักรยาน', 'count' => $bicycleCount, 'color' => 'warning']
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-2">
                    <h6 class="text-muted small mb-1">{{ $stat['label'] }}</h6>
                    <h3 class="fw-bold text-{{ $stat['color'] }} mb-0">{{ $stat['count'] ?? 0 }}</h3>
                    <small class="text-muted">คัน</small>
                </div>
            </div>
        </div>
        @endforeach
        
        <div class="col-6 col-md-3">
            <div class="card text-center shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-2">
                    <h6 class="text-muted small mb-1">ช่องจอดคงเหลือ</h6>
                    <form action="{{ route('admin.updateSlots') }}" method="POST" class="d-flex flex-column align-items-center gap-1">
                        @csrf
                        <div class="input-group input-group-sm justify-content-center">
                            <input type="number" name="total_slots" value="{{ $slots->total_slots ?? 0 }}" 
                                   class="form-control text-center border-secondary fw-bold" 
                                   style="max-width: 80px;" min="0">
                            <button type="submit" class="btn btn-dark px-2">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. แผงควบคุม: จัดลำดับมือถือและแก้ปัญหาข้อความหายตอน Hover --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body p-3">
            <div class="row g-2">
                
                {{-- ส่วนที่ 1: ตัวเลือกสีและชุด (บนสุด) --}}
                <div class="col-12 col-md-6 order-1">
                    <div class="row g-2">
                        <div class="col-6">
                            <select name="color_theme" form="bulk_print_form" class="form-select border-danger text-danger fw-bold shadow-none">
                                <option value="orange">แบบที่ 1 (ส้ม)</option>
                                <option value="red">แบบที่ 2 (แดง)</option>
                                <option value="blue">แบบที่ 3 (ฟ้า)</option>
                                <option value="green">แบบที่ 4 (เขียว)</option>
                                <option value="yellow">แบบที่ 5 (เหลือง)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="offset" form="bulk_print_form" class="form-select border-primary text-primary fw-bold shadow-none">
                                @for ($i = 0; $i < 1500; $i += 300)
                                    <option value="{{ $i }}">ชุดที่ {{ ($i/300)+1 }} ({{ $i+1 }}-{{ $i+300 }})</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 2: ปุ่มพิมพ์ชุดใหญ่ --}}
                <div class="col-12 col-md-6 order-2">
                    <form id="bulk_print_form" action="{{ route('admin.stickers.bulk') }}" method="GET" target="_blank">
                        <button type="submit" class="btn btn-danger w-100 shadow-sm fw-bold py-2 border-0 opacity-100">
                            พิมพ์สติ๊กเกอร์ชุดใหญ่
                        </button>
                    </form>
                </div>

                {{-- ส่วนที่ 3: ปุ่ม Backup และ ล้างระบบ (แก้ปัญหา Hover) --}}
                <div class="col-12 col-md-6 order-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('admin.export') }}" class="btn btn-success w-100 shadow-sm fw-bold py-2 border-0 d-flex align-items-center justify-content-center text-white text-decoration-none opacity-100">
                                Backup Excel
                            </a>
                        </div>
                        <div class="col-6">
                            <form action="{{ route('admin.clearAllStudents') }}" method="POST" onsubmit="return confirm('⚠️ ยืนยันการล้างข้อมูลทั้งหมด?')" class="h-100">
                                @csrf @method('DELETE')
                                {{-- ✅ ปรับเป็นปุ่มสีแดงอ่อน (bg-danger-subtle) เพื่อให้ข้อความแดงเข้มชัดเจนตลอดเวลา แม้จะเอาเมาส์ไปชี้ --}}
                                <button type="submit" class="btn w-100 shadow-sm fw-bold py-2 border border-danger text-danger bg-danger-subtle" style="transition: none;">
                                    ล้างระบบ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 4: ปุ่มจัดการผู้ใช้งาน --}}
                <div class="col-12 col-md-6 order-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-info text-white w-100 shadow-sm fw-bold py-2 border-0 d-flex align-items-center justify-content-center text-decoration-none opacity-100">
                        จัดการผู้ใช้งาน
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- 4. ส่วนค้นหา --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-12 col-md-3">
                    <select name="type" class="form-select border-0 bg-light shadow-none">
                        <option value="">-- ค้นหาตาม --</option>
                        <option value="name" {{ request('type')=='name'?'selected':'' }}>ชื่อ - นามสกุล</option>
                        <option value="student_id" {{ request('type')=='student_id'?'selected':'' }}>รหัสนักศึกษา</option>
                        <option value="sticker" {{ request('type')=='sticker'?'selected':'' }}>เลขสติ๊กเกอร์</option>
                        <option value="license" {{ request('type')=='license'?'selected':'' }}>ทะเบียนรถ</option>
                        <option value="room" {{ request('type')=='room'?'selected':'' }}>ห้อง/เตียง</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <input type="text" name="search" class="form-control bg-light border-0 shadow-none" placeholder="พิมพ์คำค้นหา..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">ค้นหา</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary flex-grow-1">ล้างค่า</a>
                </div>
            </form>
        </div>
    </div>

{{-- 5. ตารางข้อมูล --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">รายชื่อนักศึกษา</h5>
            <span class="badge bg-primary rounded-pill">ทั้งหมด {{ $students->count() }} รายการ</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 14px;">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="py-3">รหัสนักศึกษา</th>
                            <th class="py-3 text-start">ชื่อ - สกุล</th>
                            <th class="py-3 bg-warning bg-opacity-10 text-dark">เลขสติ๊กเกอร์</th>
                            {{-- ✅ ย้าย ห้อง/เตียง มาไว้ตรงนี้ให้เหมือนหน้า รปภ. --}}
                            <th class="py-3">ห้อง/เตียง</th> 
                            <th class="py-3">ทะเบียนรถ</th>
                            <th class="py-3">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($students as $stu)
                            <tr>
                                <td class="fw-bold text-primary">{{ $stu->student_id }}</td>
                                <td class="text-start" style="white-space: nowrap;">
                                    {{ $stu->prefix }}{{ $stu->first_name }} {{ $stu->last_name }}
                                </td>
                                <td class="fw-bold text-danger bg-warning bg-opacity-10">
                                    {{ $stu->sticker_number ?? '-' }}
                                </td>
                                {{-- ✅ แก้เป็น room_bed เพื่อให้ข้อมูลเด้งกลับมาโชว์ครับ --}}
                                <td>{{ $stu->room_bed ?? '-' }}</td>
                                <td>
                                    @foreach($stu->vehicles as $v)
                                        <span class="badge bg-light text-dark border mb-1">{{ $v->license_alpha }} {{ $v->license_number }}</span><br>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.student.show', $stu->id) }}" class="btn btn-sm btn-info text-white px-2 border-0 shadow-sm">ดูข้อมูล</a>
                                        <a href="{{ route('admin.edit', $stu->id) }}" class="btn btn-sm btn-warning px-2 border-0 shadow-sm">แก้ไข</a>
                                        <form action="{{ route('admin.student.destroy', $stu->id) }}" method="POST" onsubmit="return confirm('ลบข้อมูลนักศึกษาคนนี้?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger px-2 border-0 shadow-sm">ลบ</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">ไม่พบข้อมูล</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ส่วนการแสดงผลบนมือถือ (Mobile View) --}}
            <div class="d-block d-md-none bg-light p-2">
                @foreach($students as $stu)
                    <div class="card mb-2 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div style="overflow: hidden;">
                                    <h6 class="fw-bold mb-0 text-dark" style="white-space: nowrap;">{{ $stu->prefix }}{{ $stu->first_name }} {{ $stu->last_name }}</h6>
                                    <small class="text-muted">รหัส: <span class="text-primary">{{ $stu->student_id }}</span></small>
                                </div>
                                <span class="badge bg-warning text-dark border border-warning shadow-sm">{{ $stu->sticker_number ?? '0000' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                {{-- ✅ แก้ไขเป็น room_bed สำหรับหน้าจอมือถือด้วยครับ --}}
                                <div class="small text-muted">ห้อง: {{ $stu->room_bed ?? '-' }}</div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.student.show', $stu->id) }}" class="btn btn-sm btn-info text-white px-2 shadow-none border-0">ดูข้อมูล</a>
                                    <a href="{{ route('admin.edit', $stu->id) }}" class="btn btn-sm btn-warning px-2 shadow-none border-0">แก้ไข</a>
                                    <form action="{{ route('admin.student.destroy', $stu->id) }}" method="POST" onsubmit="return confirm('ลบ?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger px-2 shadow-none border-0">ลบ</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
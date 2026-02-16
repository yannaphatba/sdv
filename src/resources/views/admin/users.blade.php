@extends('layouts.app')
@section('title', 'จัดการข้อมูลผู้ใช้')

@section('content')
<div class="container mt-4 mb-5">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-primary mb-0">ระบบจัดการผู้ใช้</h4>
            <small class="text-muted">เพิ่ม ลบ แก้ไข บัญชีผู้ดูแลระบบและ รปภ.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                ย้อนกลับ
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success rounded-pill px-3 shadow-sm">
                + เพิ่มผู้ใช้ใหม่
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2">
                <div class="col-12 col-md-9">
                    <input type="text" name="search" class="form-control border-0 bg-light" placeholder="ค้นหา username " value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">ค้นหา</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary flex-grow-1">ดูทั้งหมด</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ตารางสำหรับ Desktop/iPad (ซ่อนในมือถือ) --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="py-3">ID</th>
                            <th>Username</th>
                            <th>สิทธิ์ (Role)</th>
                            <th>วันที่สร้าง</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- เรียงจากน้อยไปมาก --}}
                        @forelse($users->sortBy('id') as $user)
                        <tr>
                            <td class="text-center fw-bold text-muted">#{{ $user->id }}</td>
                            <td class="fw-bold text-primary">{{ $user->username }}</td>
                            <td class="text-center">
                                @if($user->role == 'admin')
                                    <span class="badge bg-danger rounded-pill px-3">Admin</span>
                                @elseif($user->role == 'security')
                                    <span class="badge bg-info text-dark rounded-pill px-3">Security</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td class="text-center small text-muted">{{ $user->created_at }}</td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- ✅ แก้ไขลิงก์: ส่งแค่ id --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning text-dark shadow-sm px-3">
                                        แก้ไข
                                    </a>
                                    {{-- ✅ แก้ไขลิงก์: ส่งแค่ id --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบผู้ใช้ {{ $user->username }}?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger shadow-sm px-3">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">ไม่พบข้อมูลผู้ใช้</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- การ์ดสำหรับมือถือ Mobile (ซ่อนใน Desktop) --}}
    <div class="d-block d-md-none">
        @forelse($users->sortBy('id') as $user)
        <div class="card mb-3 border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-primary mb-0">{{ $user->username }}</h5>
                    @if($user->role == 'admin')
                        <span class="badge bg-danger rounded-pill">Admin</span>
                    @elseif($user->role == 'security')
                        <span class="badge bg-info text-dark rounded-pill">Security</span>
                    @else
                        <span class="badge bg-secondary rounded-pill">{{ $user->role }}</span>
                    @endif
                </div>
                
                <div class="text-muted small mb-3 border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between">
                        <span>ID:</span> <strong>#{{ $user->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>สร้าง:</span> <span>{{ $user->created_at }}</span>
                    </div>
                </div>

                <div class="d-grid gap-2 d-flex">
                    {{-- ✅ แก้ไขลิงก์: ส่งแค่ id --}}
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning flex-grow-1 text-dark fw-bold shadow-sm">
                        แก้ไข
                    </a>
                    {{-- ✅ แก้ไขลิงก์: ส่งแค่ id --}}
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('ยืนยันลบ?');" class="flex-grow-1">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger w-100 fw-bold shadow-sm">ลบ</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
            <div class="text-center py-5 text-muted">ไม่พบข้อมูลผู้ใช้</div>
        @endforelse
    </div>

</div>
@endsection
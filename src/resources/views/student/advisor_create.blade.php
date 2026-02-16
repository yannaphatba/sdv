@extends('layouts.app')
@section('title', 'เพิ่มอาจารย์ที่ปรึกษา')

@section('content')
<div class="container mt-4 d-flex justify-content-center">

    <div class="card shadow-sm" style="max-width: 600px; width: 100%;">
        <div class="card-body">

            <h3 class="mb-4 text-center">เพิ่มอาจารย์ที่ปรึกษา</h3>

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.advisor.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">ชื่ออาจารย์</label>
                    <input type="text" name="name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" name="phone" class="form-control" required placeholder="เช่น 0812345678">
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('student.view') }}" class="btn btn-secondary">ย้อนกลับ</a>
                    <button type="submit" class="btn btn-success">บันทึก</button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

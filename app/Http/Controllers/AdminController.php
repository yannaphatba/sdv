<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Vehicle;
use App\Models\ParkingSlot;
use App\Models\Advisor;
use App\Models\Faculty;
use App\Models\Major;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// สำหรับการ Export Excel
use App\Exports\StudentExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Dashboard: แสดงข้อมูลและระบบค้นหา
     */
    public function dashboard(Request $request)
    {
        $q = Student::query();

        if ($request->filled('type') && $request->filled('search')) {
            $search = trim($request->search);

            if ($request->type === 'qrcode') {
                $q->where('qr_code_value', $search);
            } elseif ($request->type === 'sticker') {
                $q->where('sticker_number', $search);
            } elseif ($request->type === 'license') {
                $ids = Vehicle::where('license_number', 'like', "%{$search}%")->pluck('student_id');
                $q->whereIn('id', $ids);
            } elseif ($request->type === 'name') {
                $q->where(function ($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$search}%"]);
                });
            } elseif ($request->type === 'room') {
                $q->where('room_bed', 'like', "%{$search}%");
            } elseif ($request->type === 'student_id') {
                $q->where('student_id', 'like', "%{$search}%");
            }
        }

        $students = $q->orderBy('id', 'asc')->get();

        return view('admin.dashboard', [
            'students'        => $students,
            'motorcycleCount' => Vehicle::where('vehicle_type', 'like', '%จักรยานยนต์%')->count(),
            'carCount'        => Vehicle::where('vehicle_type', 'like', '%รถยนต์%')->count(),
            'bicycleCount'    => Vehicle::where('vehicle_type', 'like', '%จักรยาน%')
                ->where('vehicle_type', 'not like', '%จักรยานยนต์%')->count(),
            'total'           => Vehicle::count(),
            'slots'           => ParkingSlot::firstOrCreate([], ['total_slots' => 100]),
        ]);
    }

    /**
     * อัปเดตจำนวนช่องจอดรถ
     */
    public function updateSlots(Request $request)
    {
        $request->validate([
            'total_slots' => 'required|integer|min:0',
        ]);

        $slot = ParkingSlot::firstOrCreate([]);
        $slot->update(['total_slots' => $request->total_slots]);

        return back()->with('success', 'อัปเดตจำนวนช่องจอดรถเรียบร้อย');
    }

    /**
     * ฟอร์มเพิ่มนักศึกษา
     */
    public function create()
    {
        return view('admin.create', [
            'advisors'  => Advisor::all(),
            'faculties' => Faculty::orderBy('name')->get(),
            'majors'    => Major::orderBy('name')->get(),
        ]);
    }

    /**
     * บันทึกข้อมูลนักศึกษาใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefix'       => 'nullable|string|max:10',
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'student_id'   => 'required|string|max:20|unique:students,student_id',
            'room_bed'     => 'nullable|string|max:20',
            'phone'        => 'nullable|string|max:20',
            'faculty_id'   => 'nullable|exists:faculties,id',
            'major_id'     => 'nullable|exists:majors,id',
            'advisor_id'   => 'nullable|exists:advisors,id',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sticker_number' => 'nullable|numeric|digits_between:1,4|unique:students,sticker_number',
        ]);

        $user = User::create([
            'username' => $request->student_id,
            'password' => Hash::make('12345678'),
            'role'     => 'student',
        ]);

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        if (!empty($validated['sticker_number'])) {
            $validated['qr_code_value'] = $validated['sticker_number'];
        }

        $validated['user_id'] = $user->id;
        $student = Student::create($validated);

        if ($request->has('vehicle_type')) {
            foreach ($request->vehicle_type as $i => $type) {
                if (empty($type)) continue;

                $vehicle = new Vehicle([
                    'student_id'       => $student->id,
                    'vehicle_type'     => $type,
                    'license_alpha'    => $request->license_alpha[$i] ?? null,
                    'license_number'   => $request->license_number[$i] ?? null,
                    'license_province' => $request->license_province[$i] ?? null,
                    'brand'            => $request->brand[$i] ?? null,
                    'model'            => $request->model[$i] ?? null,
                    'color'            => $request->color[$i] ?? null,
                ]);

                if ($request->hasFile("vehicle_image.$i")) {
                    $vehicle->vehicle_image = $request->file("vehicle_image.$i")->store('vehicles', 'public');
                }
                $vehicle->save();
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'เพิ่มข้อมูลนักศึกษาเรียบร้อย');
    }

    public function edit($id)
    {
        return view('admin.edit', [
            'student'   => Student::findOrFail($id),
            'vehicles'  => Vehicle::where('student_id', $id)->get(),
            'advisors'  => Advisor::orderBy('name')->get(),
            'faculties' => Faculty::orderBy('name')->get(),
            'majors'    => Major::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $old_student_id = $student->student_id;

        // 1. Validation ข้อมูลส่วนตัวและรูปโปรไฟล์
        $validated = $request->validate([
            'prefix'         => 'nullable|string|max:10',
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'student_id'     => "required|string|max:20|unique:students,student_id,$id,id",
            'room_bed'       => 'nullable|string|max:20',
            'phone'          => 'nullable|string|max:20',
            'faculty_id'     => 'nullable|exists:faculties,id',
            'major_id'       => 'nullable|exists:majors,id',
            'advisor_id'     => 'nullable|exists:advisors,id',
            'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sticker_number' => "nullable|numeric|digits_between:1,4|unique:students,sticker_number,$id,id",
        ]);

        // 2. ✅ จัดการรูปภาพนักศึกษา (กันรูปหายเมื่อมีการอัปเดตข้อมูลอื่น)
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        // 3. ✅ ซิงค์ Username เมื่อรหัสนักศึกษาเปลี่ยน
        if ($old_student_id !== $request->student_id) {
            User::where('username', $old_student_id)->update(['username' => $request->student_id]);
        }

        // 4. ✅ อัปเดตค่า QR Code ตามเลขสติ๊กเกอร์
        if (isset($validated['sticker_number'])) {
            $validated['qr_code_value'] = $validated['sticker_number'];
        }

        // 5. บันทึกข้อมูลหลักของนักศึกษา
        $student->update($validated);

        // --- 🚗 ส่วนจัดการข้อมูลรถ (ฉบับแก้ไขให้บันทึกได้ทุกกรณี) ---

        // 1. ดึงชื่อไฟล์รูปเดิมเก็บไว้ก่อน
        $oldVehicleImages = $student->vehicles()->pluck('vehicle_image', 'id')->toArray();

        // 2. ล้างข้อมูลรถเดิมออก
        $student->vehicles()->delete();

        // 3. จัดการรถคันเดิม (Existing)
        if ($request->has('vehicle_type_existing')) {
            foreach ($request->vehicle_type_existing as $i => $type) {
                // ✅ เอา if (!empty($alpha)) ออก เพื่อให้บันทึกได้แม้จะไม่ได้กรอกทะเบียน
                $vehicleId = $request->vehicle_ids[$i] ?? null;

                $vehicleData = [
                    'vehicle_type'     => $type ?? 'รถจักรยานยนต์',
                    'license_alpha'    => $request->license_alpha_existing[$i] ?? '-', // ✅ ถ้าว่างให้ใส่ขีด
                    'license_number'   => $request->license_number_existing[$i] ?? '-',
                    'license_province' => $request->license_province_existing[$i] ?? '-',
                    'brand'            => $request->brand_existing[$i] ?? '-',
                    'model'            => $request->model_existing[$i] ?? 'ไม่ระบุ', // ✅ กัน Error 1364
                    'color'            => $request->color_existing[$i] ?? '-',
                    'vehicle_image'    => $oldVehicleImages[$vehicleId] ?? null,
                ];

                if ($request->hasFile("vehicle_image_existing.$i")) {
                    $vehicleData['vehicle_image'] = $request->file("vehicle_image_existing.$i")->store('vehicles', 'public');
                }
                $student->vehicles()->create($vehicleData);
            }
        }

        // 4. จัดการรถใหม่ (New)
        if ($request->has('vehicle_type')) {
            foreach ($request->vehicle_type as $i => $type) {
                // ✅ เปลี่ยนมาเช็คที่ประเภทรถแทน ถ้ามีการกดเพิ่มรถใหม่มา ต้องบันทึกให้
                $newVehicle = [
                    'vehicle_type'     => $type,
                    'license_alpha'    => $request->license_alpha[$i] ?? '-',
                    'license_number'   => $request->license_number[$i] ?? '-',
                    'license_province' => $request->license_province[$i] ?? '-',
                    'brand'            => $request->brand[$i] ?? '-',
                    'model'            => $request->model[$i] ?? 'ไม่ระบุ',
                    'color'            => $request->color[$i] ?? '-',
                ];

                if ($request->hasFile("vehicle_image.$i")) {
                    $newVehicle['vehicle_image'] = $request->file("vehicle_image.$i")->store('vehicles', 'public');
                }
                $student->vehicles()->create($newVehicle);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'อัปเดตข้อมูลนักศึกษา รูปภาพ และรถทั้งหมดเรียบร้อยแล้ว');
    }

    public function deleteVehicle($id)
    {
        Vehicle::findOrFail($id)->delete();
        return back()->with('success', 'ลบรถเรียบร้อย');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        User::where('id', $student->user_id)->delete();
        $student->delete();
        return redirect()->route('admin.dashboard')->with('success', 'ลบข้อมูลเรียบร้อย');
    }

    /**
     * ✅ ลบข้อมูลทั้งหมด (แบบสะอาดกริ๊บทั้งคนและรถ)
     */
    public function clearAllStudents(Request $request)
    {
        if (Auth::user()->role !== 'admin') return back();

        // 1. ลบไฟล์รูปภาพทั้งหมดทิ้ง
        Storage::disk('public')->deleteDirectory('profiles');
        Storage::disk('public')->deleteDirectory('vehicles');

        // 2. ล้างข้อมูลในตาราง (ต้องเรียงลำดับลบรถก่อนลบคนครับริว)
        \App\Models\Vehicle::query()->delete();  // 🚗 ลบข้อมูลรถทั้งหมด
        \App\Models\Student::query()->delete();  // 👨‍🎓 ลบข้อมูลนักศึกษาทั้งหมด

        // 3. ลบ User ที่เป็นนักศึกษาออกให้หมด
        \App\Models\User::where('role', 'student')->delete();

        return redirect()->route('admin.dashboard')->with('success', 'ล้างข้อมูลคนและรถทั้งหมดเรียบร้อยแล้วครับ');
    }

    public function exportStudents()
    {
        return Excel::download(new StudentExport, "student_data_" . date('Y-m-d') . ".xlsx");
    }

    public function show($id)
    {
        $student = Student::with(['faculty', 'major', 'advisor', 'vehicles'])->findOrFail($id);
        return view('admin.show', compact('student'));
    }

    /**
     * พิมพ์สติ๊กเกอร์ชุดใหญ่ (เวอร์ชันแก้สแกน QR Code และแสดงครบทุกดวง)
     */
    public function generateBulkStickers(Request $request)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $offset = (int) $request->get('offset', 0);
        $start = $offset + 1;
        $end = $offset + 300;

        $colorMap = ['orange' => '1', 'red' => '2', 'blue' => '3', 'green' => '4', 'yellow' => '5'];
        $bg_number = $colorMap[$request->get('color_theme', 'orange')] ?? '1';

        $stickers = [];
        for ($i = $start; $i <= $end; $i++) {
            $scanUrl = url("/svd/security/check-sticker/" . $i);

            // ✅ เจน QR Code เป็น Base64 โดยตรงในเครื่อง (เร็วขึ้น 100 เท่า)
            $qrCodeData = base64_encode(QrCode::format('svg')->size(150)->margin(1)->generate($scanUrl));

            $stickers[] = [
                'number' => $i,
                'qrcode' => 'data:image/svg+xml;base64,' . $qrCodeData
            ];
        }

        return Pdf::loadView('admin.bulk_stickers_pdf', [
            'stickers'  => $stickers,
            'bg_number' => $bg_number,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions(['isRemoteEnabled' => true, 'chroot' => public_path()])
            ->stream("stickers_{$start}_to_{$end}.pdf");
    }
    public function checkSticker($number)
    {
        // 🔒 1. เช็กสิทธิ์เบื้องต้น (เฉพาะ Admin และ Security เท่านั้นที่ดูได้)
        // เปลี่ยนจาก auth()->user() เป็น Auth::user()
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'security') {
            abort(403, 'ขออภัย เฉพาะเจ้าหน้าที่ รปภ. หรือผู้ดูแลระบบเท่านั้นที่สามารถเข้าถึงข้อมูลนี้ได้');
        }

        // ✅ 2. เติมเลข 0 นำหน้าให้เป็น 4 หลัก (เหมือนเดิมของริว)
        $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);

        // ✅ 3. ค้นหาข้อมูลนักศึกษา พร้อมโหลดความสัมพันธ์ (คณะ/สาขา/อาจารย์) เพื่อไม่ให้หน้า admin.show พัง
        $student = Student::with(['faculty', 'major', 'advisor', 'vehicles'])
            ->where('sticker_number', $formattedNumber)
            ->first();

        if ($student) {
            // ⭐ ส่งไปที่หน้าแสดงผล (admin.show)
            return view('admin.show', compact('student'));
        } else {
            return "<h3>ไม่พบข้อมูลการลงทะเบียน</h3><p>สติกเกอร์หมายเลข " . htmlspecialchars($number) . " ยังไม่ได้ถูกมอบให้นักศึกษาในระบบ</p>";
        }
    }
}

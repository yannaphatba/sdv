<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Vehicle;
use App\Models\Advisor;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * แสดงหน้าข้อมูลนักศึกษา
     */
    public function view()
    {
        $user = Auth::user(); // ดึงข้อมูล User ที่ Login อยู่มาเช็ก

        if (!$user) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        // ✅ 1. เช็ก Role: ถ้าไม่ใช่ student ให้แยกส่งไปหน้า Dashboard ของตัวเอง
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard'); // ไปหน้าแอดมิน
        }

        if ($user->role === 'security') {
            return redirect()->route('security.dashboard'); // ไปหน้ารปภ.
        }

        // ✅ 2. ถ้าเป็นนักศึกษา (หรือ role อื่นๆ) ถึงจะให้ทำกระบวนการด้านล่างต่อ
        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'student_id' => $user->username,
                'prefix'     => '',
                'first_name' => '',
                'last_name'  => '',
                'room_bed'   => '',
                'phone'      => '',
                'faculty_id' => null,
                'major_id'   => null,
                'advisor_id' => null,
            ]
        );

        $vehicles   = Vehicle::where('student_id', $student->id)->get();
        $advisors   = Advisor::orderBy('name')->get();
        $faculties  = Faculty::orderBy('name')->get();
        $majors     = Major::orderBy('name')->get();

        return view('student.view', compact(
            'student',
            'vehicles',
            'advisors',
            'faculties',
            'majors'
        ));
    }

    /**
     * อัปเดตข้อมูลนักศึกษา + เพิ่มรถใหม่
     */
    /**
     * อัปเดตข้อมูลนักศึกษา + ปลดล็อก คณะ/สาขา/อาจารย์
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        if (!$userId) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        // 1. ตรวจสอบข้อมูล (เพิ่ม faculty_id, major_id, advisor_id เข้าไปแล้วครับ)
        $request->validate([
            'prefix'        => 'nullable|string|max:10',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'student_id'    => 'required|string|max:20',
            'room_bed'      => 'nullable|string|max:20',
            'phone'         => 'nullable|string|max:20',
            'faculty_id'    => 'nullable|exists:faculties,id', // ตรวจสอบว่ามี ID นี้ในตารางคณะจริง
            'major_id'      => 'nullable|exists:majors,id',   // ตรวจสอบว่ามี ID นี้ในตารางสาขาจริง
            'advisor_id'    => 'nullable|exists:advisors,id', // ตรวจสอบว่ามี ID นี้ในตารางอาจารย์จริง
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $student = Student::where('id', $id)->where('user_id', $userId)->firstOrFail();

        // 2. อัปเดตข้อมูลส่วนตัว (เพิ่มข้อมูลการศึกษาเข้าไปใน array นี้แล้วครับริว)
        $updateData = $request->only([
            'prefix',
            'first_name',
            'last_name',
            'student_id',
            'room_bed',
            'phone',
            'faculty_id',
            'major_id',
            'advisor_id'
        ]);

        if ($request->hasFile('profile_image')) {
            if ($student->profile_image) {
                Storage::disk('public')->delete($student->profile_image);
            }
            $updateData['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $student->update($updateData);

        // 3. 🚗 เพิ่มรถใหม่ (แก้ไข Logic กัน Error NULL)
        if ($request->has('vehicle_type')) {
            foreach ($request->vehicle_type as $i => $type) {
                if (!empty($type)) {
                    $vehicleData = [
                        'student_id'       => $student->id, // ดึงจากก้อน $student ที่เรามีอยู่แล้ว
                        'vehicle_type'     => $type,
                        'license_alpha'    => $request->license_alpha[$i] ?? '-',    // แก้ Error 1048
                        'license_number'   => $request->license_number[$i] ?? '-',   // กันค่า Null
                        'license_province' => $request->license_province[$i] ?? '-',
                        'brand'            => $request->brand[$i] ?? '-',
                        'model'            => $request->model[$i] ?? 'ไม่ระบุ',      // แก้ Error 1364
                        'color'            => $request->color[$i] ?? '-',
                    ];

                    if ($request->hasFile("vehicle_image.$i")) {
                        $vehicleData['vehicle_image'] = $request->file("vehicle_image.$i")->store('vehicles', 'public');
                    }

                    Vehicle::create($vehicleData);
                }
            }
        }

        return redirect()->route('student.view')->with('success', 'บันทึกข้อมูลเรียบร้อยแล้ว');
    }

    /**
     * แจ้งเตือนการลบรถ
     */
    public function deleteVehicle($id)
    {
        return redirect()->route('student.view')
            ->with('error', 'หากต้องการแก้ไขหรือลบข้อมูลรถเก่า กรุณาติดต่อผู้ดูแลระบบ (Admin)');
    }

    // ================= ส่วนเพิ่มข้อมูลใหม่จากฝั่งนักศึกษา =================

    /**
     * บันทึกคณะใหม่
     */
    public function storeFaculty(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:faculties,name']);

        Faculty::create(['name' => $request->name]);

        // ✅ เปลี่ยนจาก back() เป็นส่งกลับหน้าหลัก เพื่อให้เห็นว่าข้อมูลบันทึกแล้ว
        return redirect()->route('student.view')->with('success', 'เพิ่มคณะเรียบร้อยแล้ว');
    }

    /**
     * บันทึกสาขาใหม่
     */
    public function storeMajor(Request $request)
    {
        // 1. ตรวจสอบแค่ชื่อสาขา (ต้องไม่ว่างและไม่ซ้ำ)
        $request->validate([
            'name' => 'required|string|unique:majors,name',
        ]);

        // 2. บันทึกแค่ชื่ออย่างเดียว
        Major::create([
            'name' => $request->name,
        ]);

        return redirect()->route('student.view')->with('success', 'เพิ่มสาขาเรียบร้อยแล้ว');
    }

    /**
     * บันทึกอาจารย์ใหม่ (ฉบับเขียนทับอันเดิม)
     */
    public function storeAdvisor(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);
        Advisor::create([
            'name'  => $request->name,
            'phone' => $request->phone
        ]);
        return redirect()->route('student.view')->with('success', 'เพิ่มรายชื่ออาจารย์เรียบร้อยแล้ว');
    }
    public function facultyCreate()
    {
        return view('student.faculty_create');
    }

    public function majorCreate()
    {
        return view('student.major_create');
    }

    public function advisorCreate()
    {
        return view('student.advisor_create');
    }
}

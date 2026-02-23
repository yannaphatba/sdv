<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Vehicle;
use App\Models\Advisor;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $advisors   = Advisor::with('majors')->orderBy('name')->get();
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

        $disk = 's3';

        if (!$userId) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        // 1. ตรวจสอบข้อมูล (เพิ่ม faculty_id, major_id, advisor_id เข้าไปแล้วครับ)
        $validator = Validator::make($request->all(), [
            'prefix'        => 'nullable|string|max:10',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'student_id'    => 'required|regex:/^\d+(\-\d+)*$/|max:20',
            'room_bed'      => 'nullable|regex:/^\d+(\/\d+)?$/|max:20',
            'phone'         => 'nullable|regex:/^\d+$/|max:20',
            'faculty_id'    => ['nullable', Rule::exists('faculties', 'id')],
            'major_id'      => ['nullable', Rule::exists('majors', 'id')],
            'advisor_id'    => ['nullable', Rule::exists('advisors', 'id')],
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'license_number.*' => 'nullable|regex:/^\d+$/|max:10',
        ], [
            'student_id.regex' => 'รหัสนักศึกษาต้องเป็นตัวเลขและสามารถใส่ - ได้',
            'room_bed.regex' => 'เลขห้อง/เตียงต้องเป็นตัวเลขและสามารถใส่ / ได้หนึ่งครั้ง',
            'phone.regex' => 'เบอร์โทรต้องเป็นตัวเลขเท่านั้น',
            'license_number.*.regex' => 'ทะเบียนชุดตัวเลขต้องเป็นตัวเลขเท่านั้น',
        ]);

        $validator->after(function ($validator) use ($request) {
            $facultyId = $request->input('faculty_id');
            $majorId = $request->input('major_id');
            $advisorId = $request->input('advisor_id');

            if ($majorId && !$facultyId) {
                $validator->errors()->add('major_id', 'กรุณาเลือกคณะก่อนเลือกสาขา');
            }

            if ($advisorId && !$majorId) {
                $validator->errors()->add('advisor_id', 'กรุณาเลือกสาขาก่อนเลือกอาจารย์ที่ปรึกษา');
            }

            if ($majorId && $facultyId) {
                $majorMatchesFaculty = Major::where('id', $majorId)
                    ->where('faculty_id', $facultyId)
                    ->exists();

                if (!$majorMatchesFaculty) {
                    $validator->errors()->add('major_id', 'สาขาที่เลือกไม่อยู่ในคณะที่เลือก');
                }
            }

            if ($advisorId && $majorId) {
                $advisorMatchesMajor = DB::table('advisor_major')
                    ->where('advisor_id', $advisorId)
                    ->where('major_id', $majorId)
                    ->exists();

                if (!$advisorMatchesMajor) {
                    $validator->errors()->add('advisor_id', 'อาจารย์ที่เลือกไม่อยู่ในสาขาที่เลือก');
                }
            }
        });

        $validator->validate();

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
                Storage::disk($disk)->delete($student->profile_image);
            }
            $updateData['profile_image'] = $request->file('profile_image')->store('profiles', $disk);
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
                        $vehicleData['vehicle_image'] = $request->file("vehicle_image.$i")->store('vehicles', $disk);
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
            'major_id' => 'required|exists:majors,id',
        ]);
        $advisor = Advisor::create([
            'name'  => $request->name,
            'phone' => $request->phone
        ]);
        $advisor->majors()->sync([$request->major_id]);
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
        $majors = Major::orderBy('name')->get();
        return view('student.advisor_create', compact('majors'));
    }
}

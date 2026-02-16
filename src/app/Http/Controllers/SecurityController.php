<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Vehicle;
use App\Models\ParkingSlot;

class SecurityController extends Controller
{
    /**
     * หน้าหลักของระบบรักษาความปลอดภัย (ดูข้อมูลนักศึกษา + ค้นหา)
     */
    public function dashboard(Request $request)
    {
        //  โหลดนักศึกษาพร้อมความสัมพันธ์ vehicles
        $q = Student::with('vehicles');

        //  ถ้ามีการค้นหา
        if ($request->filled('type') && $request->filled('search')) {
            $search = trim($request->search);

            switch ($request->type) {
                case 'qrcode':
                case 'sticker':
                    //  ค้นหาจากตาราง Student โดยตรง
                    $q->where(function ($query) use ($search) {
                        $query->where('sticker_number', $search)
                              ->orWhere('qr_code_value', $search);
                    });
                    break;

                case 'license':
                    //  ค้นหาด้วยหมายเลขทะเบียน (เช็คที่ตารางรถ)
                    $q->whereHas('vehicles', fn($v) =>
                        $v->where('license_number', 'like', "%{$search}%")
                          ->orWhere('license_alpha', 'like', "%{$search}%")
                          ->orWhereRaw("CONCAT(license_alpha, '', license_number) LIKE ?", ["%{$search}%"]) // ค้นหาแบบติดกัน กก1234
                          ->orWhereRaw("CONCAT(license_alpha, ' ', license_number) LIKE ?", ["%{$search}%"]) // ค้นหาแบบมีวรรค กก 1234
                    );
                    break;

                case 'name':
                    //  ค้นหาด้วยชื่อ / นามสกุล
                    $q->where(function ($sub) use ($search) {
                        $sub->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                    break;

                case 'student_id': 
                    //ค้นหาด้วยรหัสนักศึกษา
                    $q->where('student_id', 'like', "%{$search}%");
                    break;

                case 'room':
                    //ค้นหาด้วยเลขห้อง/เตียง
                    $q->where('room_bed', 'like', "%{$search}%");
                    break;
            }
        }

        //ดึงข้อมูลเรียงตาม id
        $students = $q->orderBy('id', 'asc')->get();

        //  นับสถิติรถ
        $motorcycleCount = Vehicle::where('vehicle_type', 'like', '%จักรยานยนต์%')->count();
        $carCount        = Vehicle::where('vehicle_type', 'like', '%รถยนต์%')->count();
        $bicycleCount    = Vehicle::where('vehicle_type', 'like', '%จักรยาน%')
                                  ->where('vehicle_type', 'not like', '%จักรยานยนต์%')
                                  ->count();
        $total           = Vehicle::count();

        //  ตรวจสอบจำนวนช่องจอด (ถ้าไม่มีให้สร้าง)
        $slots = ParkingSlot::first();
        if (!$slots) {
            $slots = ParkingSlot::create(['total_slots' => 500]);
        }

        //  ส่งข้อมูลไปหน้า view
        return view('security.dashboard', compact(
            'students',
            'motorcycleCount',
            'carCount',
            'bicycleCount',
            'total',
            'slots'
        ));
    }

    /**
     * ฟังก์ชันสำหรับดูข้อมูลนักศึกษาอย่างละเอียด (Show)
     */
    public function show($id)
    {
        // ดึงข้อมูลพร้อมความสัมพันธ์ทั้งหมด
        $student = Student::with(['faculty', 'major', 'advisor', 'vehicles'])->findOrFail($id);
        
        // ส่งไปที่หน้า View ของ Security
        return view('security.show', compact('student'));
    }
}
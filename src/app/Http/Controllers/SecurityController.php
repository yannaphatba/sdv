<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Vehicle;
use App\Models\ParkingSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB;

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
        if ($request->filled('search')) {
            $search = trim($request->search);
            $value = $search;

            if (preg_match('/check-sticker\/(\d+)/', $search, $matches)) {
                $value = $matches[1];
            }
            if (ctype_digit($value) && strlen($value) < 4) {
                $value = str_pad($value, 4, '0', STR_PAD_LEFT);
            }

            $q->where(function ($query) use ($search, $value) {
                $query->where('student_id', 'like', "%{$search}%")
                    ->orWhere('room_bed', 'like', "%{$search}%")
                    ->orWhere('sticker_number', $value)
                    ->orWhere('qr_code_value', $value)
                    ->orWhere(function ($name) use ($search) {
                        $name->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
            });

                        $q->orWhereHas('vehicles', fn($v) =>
                                $v->where('license_number', 'like', "%{$search}%")
                                    ->orWhere('license_alpha', 'like', "%{$search}%")
                                    ->orWhere('sticker_number', $value)
                                    ->orWhereRaw("CONCAT(license_alpha, '', license_number) LIKE ?", ["%{$search}%"])
                                    ->orWhereRaw("CONCAT(license_alpha, ' ', license_number) LIKE ?", ["%{$search}%"])
                        );
        }

        //ดึงข้อมูลเรียงตาม id
        // Join vehicles table เพื่อดึง sticker_number ที่น้อยที่สุดของแต่ละ student
        $students = $q->leftJoin('vehicles', 'students.id', '=', 'vehicles.student_id')
            ->select('students.id', 'students.student_id', 'students.prefix', 'students.first_name', 'students.last_name', 'students.room_bed', 'students.qr_code_value', DB::raw("MIN(CASE WHEN vehicles.sticker_number IS NULL OR vehicles.sticker_number = '' OR vehicles.sticker_number = '0000' THEN NULL ELSE LPAD(vehicles.sticker_number, 4, '0') END) as min_sticker_number"))
            ->groupBy('students.id', 'students.student_id', 'students.prefix', 'students.first_name', 'students.last_name', 'students.room_bed', 'students.qr_code_value')
            ->orderByRaw("CASE WHEN min_sticker_number IS NULL THEN 1 ELSE 0 END ASC")
            ->orderBy('min_sticker_number', 'ASC')
            ->orderBy('students.id', 'asc')
            ->get();

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
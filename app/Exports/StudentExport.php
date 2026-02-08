<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // ดึงข้อมูลนักศึกษาพร้อมข้อมูลที่เกี่ยวข้อง
        return Student::with(['faculty', 'major', 'advisor', 'vehicles'])->get();
    }

    /**
     * กำหนดหัวตาราง (Header)
     */
    public function headings(): array
    {
        return [
            'หมายเลขสติ๊กเกอร์', 
            'รหัสนักศึกษา',
            'ชื่อ - นามสกุล',
            'เบอร์โทรศัพท์',
            'ห้อง/เตียง',
            'คณะ',
            'สาขา',
            'อาจารย์ที่ปรึกษา',
            'ประเภทรถ',
            'เลขทะเบียน',
            'จังหวัด',
            'ยี่ห้อ',
            'รุ่น',
            'สีรถ',
            'วันที่ลงทะเบียน',
        ];
    }

    /**
     * จัดรูปแบบข้อมูลแต่ละแถว (Mapping)
     */
    public function map($student): array
    {
        $rows = [];

        // ถ้านักศึกษามีรถหลายคัน ให้วนลูปเก็บข้อมูลทีละคัน
        if ($student->vehicles->count() > 0) {
            foreach ($student->vehicles as $vehicle) {
                $rows[] = [
                    $student->sticker_number ?? '-', // ✅ 2. ดึงเลขสติ๊กเกอร์มาแสดง (ถ้าไม่มีให้ขึ้น -)
                    $student->student_id,
                    $student->prefix . ' ' . $student->first_name . ' ' . $student->last_name,
                    $student->phone,
                    $student->room_bed,
                    $student->faculty->name ?? '-',
                    $student->major->name ?? '-',
                    $student->advisor ? $student->advisor->name : '-',
                    $vehicle->vehicle_type,
                    $vehicle->license_alpha . ' ' . $vehicle->license_number, // รวมหมวดกับเลข
                    $vehicle->license_province,
                    $vehicle->brand,
                    $vehicle->model,
                    $vehicle->color,
                    $student->created_at->format('d/m/Y H:i'),
                ];
            }
        } else {
            // กรณีไม่มีรถ ก็ให้แสดงข้อมูลนักศึกษาอย่างเดียว
            $rows[] = [
                $student->sticker_number ?? '-', // ✅ เพิ่มตรงนี้ด้วยเช่นกัน
                $student->student_id,
                $student->prefix . ' ' . $student->first_name . ' ' . $student->last_name,
                $student->phone,
                $student->room_bed,
                $student->faculty->name ?? '-',
                $student->major->name ?? '-',
                $student->advisor ? $student->advisor->name : '-',
                '-', // ไม่มีรถ
                '-',
                '-',
                '-',
                '-',
                '-',
                $student->created_at->format('d/m/Y H:i'),
            ];
        }

        return $rows;
    }
}
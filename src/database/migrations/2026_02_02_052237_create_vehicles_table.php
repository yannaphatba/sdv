<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            
            // เชื่อมกับตาราง students (ตามชื่อคอลัมน์ student_id ในรูป)
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            $table->string('vehicle_type');     // ประเภทรถ (เช่น รถจักรยานยนต์)
            $table->string('license_alpha');    // หมวดอักษร (เช่น 1กค)
            $table->string('license_number');   // เลขทะเบียน (เช่น 8100)
            $table->string('license_province'); // จังหวัด (เช่น นครราชสีมา)
            $table->string('brand');            // ยี่ห้อ (เช่น Honda)
            $table->string('model');            // รุ่น (เช่น Wave 125i)
            $table->string('color');            // สี (เช่น น้ำเงิน)
            $table->string('vehicle_image')->nullable(); // รูปภาพ (nullable เผื่อบางคันไม่มีรูป)
            
            // เพิ่ม status เผื่อไว้สำหรับการอนุมัติ (Pending/Approved)
            // ถ้าในรูปเก่าไม่มี แต่ระบบใหม่อาจจะจำเป็นต้องใช้ครับ
            $table->string('status')->default('pending'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
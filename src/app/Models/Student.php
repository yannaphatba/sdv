<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prefix',
        'first_name',
        'last_name',
        'student_id',
        'room_bed',
        'phone',
        'advisor_id',
        'profile_image',
        'faculty_id',   // ⭐ Foreign key จริง
        'major_id',     // ⭐ Foreign key จริง
        'sticker_number',
        'qr_code_value',
    ];

    // ความสัมพันธ์กับรถ
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    // ความสัมพันธ์กับอาจารย์ที่ปรึกษา
    public function advisor()
    {
        return $this->belongsTo(Advisor::class);
    }

    // ⭐ ความสัมพันธ์คณะ
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    // ⭐ ความสัมพันธ์สาขา
    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}

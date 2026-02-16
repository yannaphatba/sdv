<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'vehicle_type',
        'license_alpha',
        'license_number',
        'license_province',
        'brand',
        'model',
        'color',
        'vehicle_image'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

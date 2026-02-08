<?php

namespace App\Models;

// ✅ 1. เพิ่มบรรทัดนี้ เพื่อเรียกใช้ระบบ Factory
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // เพิ่ม Notifiable ไว้ด้วยตามมาตรฐาน (เผื่ออนาคตใช้แจ้งเตือน)

class User extends Authenticatable
{
    // ✅ 2. เพิ่มบรรทัดนี้ เพื่อเปิดใช้งานในตัว Class
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }
}
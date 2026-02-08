
# Laravel Upgrade Kit (Username Login, Students CRUD)

## วิธีติดตั้งแบบอัตโนมัติ (ใช้สคริปต์)
1) ติดตั้ง Composer และ PHP 8.1+ ให้พร้อม
2) แตกไฟล์ ZIP นี้ไว้ที่โฟลเดอร์ใดก็ได้
3) รันสคริปต์ (macOS/Linux):
```bash
bash install.sh
```
หรือบน Windows PowerShell:
```powershell
./install.ps1
```

## สิ่งที่สคริปต์ทำ
- สร้างโปรเจกต์ Laravel ใหม่ `vehicle-system`
- คัดลอกไฟล์จากชุดนี้ทับลงไป (routes/controllers/models/middleware/views/migrations/css)
- รัน `composer install`, สร้าง key, และ `php artisan migrate --seed` (สร้าง admin:admin123)

## เมื่อติดตั้งเสร็จ
- แอดมินเข้าสู่ระบบ: **username:** admin / **password:** admin123
- นักศึกษาสมัครสมาชิกได้ที่ `/register` (จะได้ role student อัตโนมัติ)
- รันเซิร์ฟเวอร์:
```bash
cd vehicle-system
php artisan serve
```

## หมายเหตุ
- ระบบล็อกอินใช้ `username` (ไม่ใช่ email)
- ตารางตาม Laragon เดิมของคุณ: `users`, `students`

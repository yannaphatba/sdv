<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // ✅ เพิ่มบรรทัดนี้
use App\Mail\WelcomeMail; // ✅ เพิ่มบรรทัดนี้ (ต้องสร้างไฟล์ Mail ก่อนนะริว)
use Illuminate\Support\Facades\Log; // ✅ สำหรับบันทึก Error

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * ✅ Register: สมัครสมาชิกและส่งเมลแจ้งเตือน
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username', 
            'email'    => 'required|email', // ✅ เพิ่ม Validation สำหรับอีเมล
            'password' => 'required|confirmed|min:4',
            'role'     => 'required|in:admin,student,security' 
        ], [
            'username.unique' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'email.email' => 'กรุณากรอกรูปแบบอีเมลให้ถูกต้อง'
        ]);

        // 1. บันทึกข้อมูลลงฐานข้อมูล (เพิ่มช่อง email เข้าไปครับ)
        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,    // ✅ เพิ่มบรรทัดนี้เพื่อให้ DB มีข้อมูลเมล
            'password' => Hash::make($request->password), 
            'role'     => $request->role, 
        ]);

        // 2. 📩 ระบบส่งเมลแจ้งเตือน (เพิ่มเข้าไปใหม่แบบปลอดภัย)
        try {
            // พยายามส่งเมลไปที่ email ที่รับมาจากหน้าฟอร์ม
            Mail::to($request->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            // 🛡️ ถ้าส่งไม่สำเร็จ (เช่น เมลมั่ว/เน็ตหลุด) ให้จด Error ลง Log 
            // แต่ระบบจะไม่หยุดทำงาน (ไม่ขึ้นหน้าขาว)
            Log::error("Email sending failed: " . $e->getMessage());
        }

        return redirect('/svd/login')->with('success', 'สมัครสมาชิกสำเร็จ! ตรวจสอบกล่องจดหมายของคุณด้วยครับ');
    }

    /**
     * ✅ Login: ตรวจสอบสิทธิ์และแยกเส้นทางตาม Role (ฟังก์ชันเดิมอยู่ครบ)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->intended('/svd/admin/dashboard');
            } 
            elseif ($user->role === 'security') {
                return redirect()->intended('/svd/security/dashboard');
            }
            elseif ($user->role === 'student') {
                return redirect()->intended('/svd/student/view');
            }

            return redirect('/svd/login');
        }

        return back()->with('error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง')->withInput();
    }

    /**
     * ✅ Logout: เคลียร์ Session ทั้งหมด (ฟังก์ชันเดิมอยู่ครบ)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
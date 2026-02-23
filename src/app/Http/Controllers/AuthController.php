<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // ✅ เพิ่มบรรทัดนี้
use App\Mail\VerifyEmailMail; // ✅ เพิ่มบรรทัดนี้
use Illuminate\Support\Facades\Log; // ✅ สำหรับบันทึก Error
use Illuminate\Support\Facades\URL;

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
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $email = (string) $request->input('email');

        $existingUser = User::where('username', $request->input('username'))
            ->orWhere('email', $email)
            ->first();

        if ($existingUser && empty($existingUser->email_verified_at)) {
            $existingUser->delete();
        }

        $request->validate([
            'username' => [
                'required',
                'unique:users,username',
                'regex:/^[0-9-]+$/',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $digits = preg_replace('/\D+/', '', (string) $value);
                    if (strlen($digits) !== 12) {
                        $fail('รหัสนักศึกษาต้องมีตัวเลข 12 หลัก');
                    }

                    if (substr_count((string) $value, '-') > 1) {
                        $fail('รหัสนักศึกษาสามารถใส่เครื่องหมาย - ได้ไม่เกิน 1 ตัว');
                    }
                },
            ],
            'email'    => [
                'required',
                'email:rfc,dns',
                'unique:users,email',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $parts = explode('@', strtolower(trim((string) $value)));
                    $domain = count($parts) === 2 ? $parts[1] : '';

                    if ($domain !== 'rmuti.ac.th') {
                        $fail('กรุณาใช้อีเมลมหาวิทยาลัยที่ลงท้ายด้วย @rmuti.ac.th เท่านั้น');
                    }
                },
            ],
            'password' => 'required|confirmed|min:4',
            'role'     => 'required|in:admin,student,security' 
        ], [
            'username.unique' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'email.email' => 'กรุณากรอกรูปแบบอีเมลให้ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว'
        ]);

        // 1. บันทึกข้อมูลลงฐานข้อมูล (เพิ่มช่อง email เข้าไปครับ)
        $user = User::create([
            'username' => $request->username,
            'email'    => $request->email,    // ✅ เพิ่มบรรทัดนี้เพื่อให้ DB มีข้อมูลเมล
            'password' => Hash::make($request->password), 
            'role'     => $request->role, 
        ]);

        // 2. 📩 ระบบส่งเมลยืนยัน (เพิ่มเข้าไปใหม่แบบปลอดภัย)
        try {
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1((string) $user->email)]
            );
            Mail::to($request->email)->send(new VerifyEmailMail($user, $verifyUrl));
        } catch (\Exception $e) {
            // 🛡️ ถ้าส่งไม่สำเร็จ (เช่น เมลมั่ว/เน็ตหลุด) ให้จด Error ลง Log 
            // แต่ระบบจะไม่หยุดทำงาน (ไม่ขึ้นหน้าขาว)
            Log::error("Email sending failed: " . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'สมัครสมาชิกสำเร็จ! กรุณายืนยันอีเมลก่อนเข้าสู่ระบบ');
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
            // Clear any stale intended URL like /sdv/sdv/... from previous redirects.
            $request->session()->forget('url.intended');
            $user = Auth::user();

            if ($user->role === 'student' && empty($user->email_verified_at)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'กรุณายืนยันอีเมลก่อนเข้าสู่ระบบ');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } 
            elseif ($user->role === 'security') {
                return redirect()->route('security.dashboard');
            }
            elseif ($user->role === 'student') {
                return redirect()->route('student.view');
            }

            return redirect()->route('login');
        }

        return back()->with('error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง')->withInput();
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        if (!$request->hasValidSignature() && !$request->hasValidSignature(false)) {
            abort(403, 'ลิงก์ยืนยันไม่ถูกต้องหรือหมดอายุ');
        }

        $user = User::findOrFail($id);
        $expected = sha1((string) $user->email);

        if (!hash_equals($expected, (string) $hash)) {
            abort(403, 'ลิงก์ยืนยันไม่ถูกต้อง');
        }

        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect()->route('login')->with('success', 'ยืนยันอีเมลเรียบร้อยแล้ว สามารถเข้าสู่ระบบได้');
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
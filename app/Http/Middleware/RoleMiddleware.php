<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // ถ้ายังไม่ได้ login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // ถ้า role ไม่ตรงกัน
        if (Auth::user()->role !== $role) {
            return redirect()->route('login')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าหน้านี้');
        }

        // ผ่านการตรวจสอบ → ไปต่อ
        return $next($request);
    }
}

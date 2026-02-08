<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // ✅ 1. เพิ่มบรรทัดนี้ เพื่อเรียกใช้ระบบ Auth

class AdminUserController extends Controller
{
    /**
     * แสดงรายชื่อผู้ใช้ทั้งหมด + ค้นหา
     */
    public function index(Request $request)
    {
        $search = trim($request->search);

        $users = User::query()
            ->when($search, function($q) use ($search){
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * ฟอร์มเพิ่มผู้ใช้
     */
    public function create()
    {
        return view('admin.users-create'); 
    }

    /**
     * เพิ่มผู้ใช้ใหม่
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:3',
            'role'     => 'required|in:admin,student,security',
        ], [
            'username.unique' => "ชื่อผู้ใช้ '{$request->username}' มีอยู่ในระบบแล้ว กรุณาใช้ชื่ออื่น"
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'เพิ่มผู้ใช้ใหม่เรียบร้อย');
    }

    /**
     * ฟอร์มแก้ไข
     */
    public function edit($id) 
    {
        $user = User::findOrFail($id);
        $type = $user->role; 

        return view('admin.users-edit', compact('user', 'type'));
    }

    /**
     * อัปเดตข้อมูล
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => [
                'required', 
                'string', 
                'max:50',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:3',
            'role'     => 'required|in:admin,student,security',
        ], [
            'username.unique' => "ชื่อผู้ใช้ซ้ำกับคนอื่นในระบบ"
        ]);

        $data = [
            'username' => $request->username,
            'role'     => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'อัปเดตข้อมูลเรียบร้อย');
    }

    /**
     * ลบผู้ใช้
     */
    public function destroy($id)
    {
        // ✅ 2. แก้ตรงนี้เป็น Auth::id() ตัวแดงจะหายไปครับ
        if (Auth::id() == $id) {
            return back()->with('error', 'ไม่สามารถลบตัวเองได้ขณะล็อกอิน');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'ลบผู้ใช้เรียบร้อย');
    }
}
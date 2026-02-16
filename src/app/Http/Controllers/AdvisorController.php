<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advisor;

class AdvisorController extends Controller
{
    public function create()
    {
        return view('student.advisor_create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name'  => 'required|string|max:255',
        'phone' => 'required|string|max:50',
    ]);

    $advisor = Advisor::create([
        'name'  => $request->name,
        'phone' => $request->phone,
    ]);

    //  ต้องส่ง JSON กลับให้ fetch() ใช้ได้
    return response()->json($advisor);
}
    
}

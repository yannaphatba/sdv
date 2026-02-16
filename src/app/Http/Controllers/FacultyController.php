<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;

class FacultyController extends Controller
{
    public function store(Request $request)
{
    $major = Faculty::create([
        'name' => $request->name
    ]);

    return response()->json([
        'id' => $major->id,
        'name' => $major->name
    ]);
}

}

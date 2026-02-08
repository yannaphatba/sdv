<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Major;

class MajorController extends Controller
{
 public function store(Request $request)
{
    $major = Major::create([
        'name' => $request->name
    ]);

    return response()->json([
        'id' => $major->id,
        'name' => $major->name
    ]);
}


}

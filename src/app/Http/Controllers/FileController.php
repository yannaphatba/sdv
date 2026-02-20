<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show(string $path)
    {
        $disk = 's3';

        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $contents = Storage::disk($disk)->get($path);
        $mimeType = 'application/octet-stream';

        return response($contents, 200, [
            'Content-Type' => $mimeType,
        ]);
    }
}

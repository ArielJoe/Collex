<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    public function index(Request $request, $filename)
    {
        $path = "event_posters/" . $filename;

        if (!Storage::exists($path)) {
            abort(404);
        }

        return response(Storage::get($path), 200)
            ->header("Content-Type", Storage::mimeType($path));
    }
}

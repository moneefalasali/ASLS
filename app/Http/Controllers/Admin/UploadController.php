<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Upload;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = Upload::orderBy('created_at','desc')->paginate(50);
        return view('admin.uploads.index', compact('uploads'));
    }

    public function show(Upload $upload)
    {
        return view('admin.uploads.show', compact('upload'));
    }
}

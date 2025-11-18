<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SignAsset;
use Illuminate\Support\Facades\Storage;

class SignAssetController extends Controller
{
    public function index()
    {
        $assets = SignAsset::orderBy('language')->orderBy('key')->paginate(50);
        return view('admin.signs.index', compact('assets'));
    }

    public function create()
    {
        return view('admin.signs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(["key"=>"required","language"=>"required","type"=>"required","text"=>"nullable"]);

        // handle uploaded file (image/svg/json for animation)
        if ($request->hasFile('src_file')) {
            $file = $request->file('src_file');
            $path = $file->store('signs', 'public');
            $data['src'] = '/storage/' . $path;
        } else {
            $data['src'] = $request->input('src');
        }

        SignAsset::create($data);
        return redirect()->route('admin.signs.index')->with('success','تم إنشاء الإشارة بنجاح');
    }

    public function edit(SignAsset $sign)
    {
        return view('admin.signs.edit', ['sign'=>$sign]);
    }

    public function update(Request $request, SignAsset $sign)
    {
        $data = $request->validate(["key"=>"required","language"=>"required","type"=>"required","text"=>"nullable"]);

        if ($request->hasFile('src_file')) {
            // delete old file if it's in storage
            try {
                $old = str_replace('/storage/','',$sign->src);
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
            } catch (\Exception $e) {}

            $file = $request->file('src_file');
            $path = $file->store('signs', 'public');
            $data['src'] = '/storage/' . $path;
        } else {
            $data['src'] = $request->input('src', $sign->src);
        }

        $sign->update($data);
        return redirect()->route('admin.signs.index')->with('success','تم تحديث الإشارة');
    }

    public function destroy(SignAsset $sign)
    {
        $sign->delete();
        return back();
    }
}

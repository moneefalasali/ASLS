<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id','desc')->paginate(50);
        return view('admin.users.index', compact('users'));
    }

    public function toggle(User $user)
    {
        $user->active = !$user->active;
        $user->save();
        return back();
    }
}

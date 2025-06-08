<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::where('is_active', true)->get();
        return view('admin.users.index', compact('roles'));
    }
} 
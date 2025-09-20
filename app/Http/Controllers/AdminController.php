<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::all();
        return view('admin.index', compact('users', 'roles'));
    }

    public function assignRole(Request $request, User $user)
    {
        $user->assignRole($request->role);
        return redirect()->back()->with('success', 'Role assigned!');
    }

    // System monitoring: logs, etc.
    public function logs()
    {
        $logs = \Illuminate\Support\Facades\Log::channel('single')->get();  // Or use spatie log viewer
        return view('admin.logs', compact('logs'));
    }
}

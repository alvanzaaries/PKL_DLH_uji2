<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->orderByDesc('id')
            ->get();

        return view('PNBP.admin.users_management.index', compact('users'));
    }

    public function create(Request $request)
    {
        return view('PNBP.admin.users_management.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // New users are always regular 'user' (no role assignment from UI)
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'user',
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(Request $request, User $user)
    {
        return view('PNBP.admin.users_management.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        // Role cannot be changed via UI; keep existing role
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Perubahan user berhasil disimpan.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $generated = Str::random(12);
        $user->password = Hash::make($generated);
        $user->save();

        return back()->with('success', 'Password berhasil di-reset. Password baru: ' . $generated);
    }
}

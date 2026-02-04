<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Menampilkan daftar pengguna untuk admin.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->orderByDesc('id')
            ->get();

        return view('admin.users_management.index', compact('users'));
    }

    /**
     * Menampilkan form tambah pengguna.
     */
    public function create(Request $request)
    {
        return view('admin.users_management.create');
    }

    /**
     * Menyimpan pengguna baru dari form admin.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:user,admin'],
        ]);

        // Admin bisa menentukan role saat membuat user baru.
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Menampilkan form edit pengguna.
     */
    public function edit(Request $request, User $user)
    {
        return view('admin.users_management.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'string', 'in:user,admin'],
        ]);

        // Admin bisa mengubah role user (termasuk menjadikan admin).
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Perubahan user berhasil disimpan.');
    }

    /**
     * Mereset password pengguna ke nilai tetap.
     */
    public function resetPassword(Request $request, User $user)
    {
        $generated = 'password';
        $user->password = Hash::make($generated);
        $user->save();

        return back()->with('success', 'Password berhasil di-reset. Password baru: ' . $generated);
    }
}

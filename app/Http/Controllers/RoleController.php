<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;       // <-- Model Role
use Spatie\Permission\Models\Permission; // <-- Model Permission
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;       // <-- Untuk Transaction

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua role.
     */
    public function index()
    {
        // Ambil roles beserta permissions yang dimiliki
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Menampilkan form untuk membuat role baru.
     */
    public function create()
    {
        // Ambil semua permission yang ada untuk ditampilkan sebagai checkbox
        $permissions = Permission::orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Menyimpan role baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:125|unique:roles,name',
            'permissions' => 'nullable|array', // Permissions opsional saat buat
            'permissions.*' => 'exists:permissions,name', // Pastikan permission valid
        ]);

        DB::beginTransaction();
        try {
            // Buat role baru
            $role = Role::create(['name' => $request->name]);

            // Berikan permissions yang dipilih (jika ada)
            if ($request->has('permissions')) {
                $role->givePermissionTo($request->permissions);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat role: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('roles.index')
                         ->with('success', 'Role baru berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit role.
     */
    public function edit(Role $role)
    {
        // Ambil semua permission yang ada
        $permissions = Permission::orderBy('name')->get();
        // Ambil permission yang sudah dimiliki role ini
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Mengupdate data role di database.
     */
    public function update(Request $request, Role $role)
    {
         // Jangan izinkan edit nama role 'Admin' (opsional, tapi aman)
        if ($role->name === 'Admin' && $request->name !== 'Admin') {
             return back()->with('error', 'Nama role Admin tidak dapat diubah.');
        }

        $request->validate([
            'name' => [
                'required', 'string', 'max:125',
                Rule::unique('roles')->ignore($role->id),
            ],
            'permissions' => 'nullable|array', // Permissions bisa jadi kosong
            'permissions.*' => 'exists:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            // Update nama role (jika berubah)
            $role->name = $request->name;
            $role->save();

            // Sinkronkan permissions (hapus yang lama, tambahkan yang baru)
            $role->syncPermissions($request->permissions ?? []);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Tampilkan pesan error spesifik jika gagal
            return back()->with('error', 'Gagal mengupdate role: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('roles.index')
                         ->with('success', 'Data role berhasil diupdate.');
    }

    /**
     * Menghapus role dari database.
     */
    public function destroy(Role $role)
    {
        // Jangan izinkan hapus role 'Admin'
        if ($role->name === 'Admin') {
            return redirect()->route('roles.index')
                             ->with('error', 'Role Admin tidak dapat dihapus.');
        }

        // Cek apakah role masih digunakan oleh user (opsional tapi aman)
        if ($role->users()->count() > 0) {
             return redirect()->route('roles.index')
                             ->with('error', 'Gagal menghapus! Role ini masih digunakan oleh pengguna.');
        }

        try {
            $role->delete();
            return redirect()->route('roles.index')
                             ->with('success', 'Role berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')
                             ->with('error', 'Gagal menghapus role: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = AdminUser::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admin_users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:super_admin,admin,editor'],
        ]);

        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Admin user created successfully.');
    }

    public function edit(AdminUser $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, AdminUser $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('admin_users')->ignore($user->id)],
            'role' => ['required', 'in:super_admin,admin,editor'],
            'is_active' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = $request->only(['name', 'email', 'role']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Admin user updated successfully.');
    }

    public function destroy(AdminUser $user)
    {
        // Prevent deleting the last super admin
        if ($user->isSuperAdmin() && AdminUser::where('role', 'super_admin')->count() <= 1) {
            return back()->withErrors(['error' => 'Cannot delete the last super admin.']);
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Admin user deleted successfully.');
    }

    public function toggleStatus(AdminUser $user)
    {
        // Prevent deactivating the last super admin
        if ($user->isSuperAdmin() && $user->is_active && AdminUser::where('role', 'super_admin')->where('is_active', true)->count() <= 1) {
            return back()->withErrors(['error' => 'Cannot deactivate the last active super admin.']);
        }

        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Admin user {$status} successfully.");
    }
}
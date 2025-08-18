<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Update last login
            $admin = Auth::guard('admin')->user();
            $admin->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function showRegister()
    {
        // Only allow registration if no super admin exists or if current user is super admin
        if (AdminUser::where('role', 'super_admin')->exists() && 
            (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->isSuperAdmin())) {
            abort(403, 'Registration is restricted.');
        }
        
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        // Check if this is the first user (will be super admin)
        $isFirstUser = !AdminUser::exists();
        
        // If not first user, check if current user is super admin
        if (!$isFirstUser && (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->isSuperAdmin())) {
            abort(403, 'Only super admins can register new users.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admin_users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:super_admin,admin,editor'],
        ]);

        $role = $isFirstUser ? 'super_admin' : $request->role;

        $admin = AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_active' => true,
        ]);

        if ($isFirstUser) {
            Auth::guard('admin')->login($admin);
            return redirect()->route('admin.dashboard')->with('success', 'Welcome! Your super admin account has been created.');
        }

        return redirect()->route('admin.users.index')->with('success', 'Admin user created successfully.');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $locale = session('locale') ?? $request->cookie('locale') ?? config('app.locale', 'en');
            $request->session()->regenerate();
            session(['locale' => $locale]);
            cookie()->queue('locale', $locale, 60 * 24 * 365);
            $user = Auth::user();

            return $this->redirectBasedOnRole($user);
        }

        return back()->withInput($request->only('username'))->withErrors([
            'username' => '❌ Invalid username or password!',
        ]);
    }

    public function logout(Request $request)
    {
        $locale = session('locale') ?? $request->cookie('locale') ?? config('app.locale', 'en');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session(['locale' => $locale]);
        cookie()->queue('locale', $locale, 60 * 24 * 365);

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    public function showChangePassword()
    {
        return view('auth.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password!']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', '✔️ Password changed successfully!');
    }

    protected function redirectBasedOnRole($user)
    {
        return match ($user->role) {
            'Admin'                       => redirect()->route('admin.dashboard'),
            'Teacher'                     => redirect()->route('teacher.marks'),
            'Parent'                      => redirect()->route('parent.reports'),
            'Accountant'                  => redirect()->route('accountant.fees'),
            'Headmaster', 'Academic Master' => redirect()->route('leader.dashboard'),
            default                       => redirect()->route('home'),
        };
    }
}

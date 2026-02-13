<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pages.auth.signin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $key = 'login.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.',
            ]);
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $user = User::where($loginType, $request->login)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);

            ActivityLog::create([
                'user_id' => $user->id ?? null,
                'action' => 'failed_login',
                'module' => 'auth',
                'record_id' => null,
                'old_values' => null,
                'new_values' => json_encode(['login' => $request->login]),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'login' => 'Username/Email atau password salah.',
            ]);
        }

        RateLimiter::clear($key);

        Auth::login($user, $request->filled('remember'));

        $user->updateLastLogin();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'module' => 'auth',
            'record_id' => $user->id,
            'old_values' => null,
            'new_values' => json_encode(['warehouse_id' => $user->warehouse_id]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath($user));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'module' => 'auth',
            'record_id' => $user->id,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectPath(User $user): string
    {
        return $user->getHomeRoute();
    }

    public function showResetPassword()
    {
        return view('pages.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // TODO: Implement email token logic
        
        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }
}
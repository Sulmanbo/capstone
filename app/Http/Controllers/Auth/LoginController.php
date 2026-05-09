<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ThreatEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:128'],
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->first();

        if (!$user) {
            AuditLog::record(
                AuditLog::LOGIN_FAILED,
                ['username_attempted' => $username, 'reason' => 'User not found']
            );
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Invalid credentials. Please try again.']);
        }

        // Auto-unlock if lockout expired
        $user->checkAndUnlock();
        $user->refresh();

        if ($user->status === 'locked') {
            $minutesLeft = max(1, now()->diffInMinutes($user->locked_until, false));
            AuditLog::record(AuditLog::LOGIN_FAILED, ['reason' => 'Account locked'], $user->id, $user->full_name);
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => "Account locked. Try again in {$minutesLeft} minute(s)."]);
        }

        if ($user->status === 'deactivated') {
            AuditLog::record(AuditLog::LOGIN_FAILED, ['reason' => 'Account deactivated'], $user->id);
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'This account has been deactivated. Contact your administrator.']);
        }

        if (!Hash::check($password, $user->password)) {
            $user->incrementFailedAttempts();
            $user->refresh();

            AuditLog::record(
                AuditLog::LOGIN_FAILED,
                ['reason' => 'Wrong password', 'failed_attempts' => $user->failed_attempts],
                $user->id, $user->full_name
            );

            if ($user->status === 'locked') {
                AuditLog::record(AuditLog::ACCOUNT_LOCKED, null, $user->id, $user->full_name);
                ThreatEvent::record('brute_force', 'critical', 'Account Locked — Brute Force',
                    "Account [{$user->username}] locked after 5 consecutive failed login attempts.", $user->id);
            }

            $remaining = max(0, 5 - $user->failed_attempts);
            $msg = $remaining > 0
                ? "Invalid credentials. {$remaining} attempt(s) remaining before lockout."
                : 'Account locked for 10 minutes due to too many failed attempts.';

            return back()->withInput($request->only('username'))->withErrors(['username' => $msg]);
        }

        // Success
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->clearFailedAttempts();
        $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);

        AuditLog::record(AuditLog::LOGIN_SUCCESS, null, $user->id, $user->full_name);

        if ($user->password_reset_required) {
            return redirect()->route('password.force-reset')
                ->with('info', 'Please set a new password before continuing.');
        }

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::record(AuditLog::LOGOUT, null, $user->id, $user->full_name);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        return match($user->role_id) {
            '04' => redirect()->route('admin.dashboard'),
            '03' => redirect()->route('registrar.dashboard'),
            '02' => redirect()->route('faculty.dashboard'),
            '01' => redirect()->route('student.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}

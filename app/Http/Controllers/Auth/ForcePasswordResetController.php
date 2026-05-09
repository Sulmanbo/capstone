<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * ForcePasswordResetController
 *
 * Handles the mandatory password reset on first login.
 * Validates all composition rules from the spec:
 *   - 8 to 64 characters
 *   - One uppercase letter
 *   - One lowercase letter
 *   - One number
 *   - One special character (@, #, $, %, ^, &, !, ?, _)
 *   - No spaces or control characters (\ or /)
 */
class ForcePasswordResetController extends Controller
{
    public function show()
    {
        // If reset is not required, bounce back to the correct dashboard
        if (!Auth::user()->password_reset_required) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.force-reset');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',                          // requires password_confirmation field
                'regex:/[A-Z]/',                      // at least one uppercase
                'regex:/[a-z]/',                      // at least one lowercase
                'regex:/[0-9]/',                      // at least one number
                'regex:/[@#$%^&!?_*]/',               // at least one allowed special char
                'not_regex:/[\s\\\\\/]/',             // no spaces, backslash, or forward slash
            ],
        ], [
            'password.regex'     => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@, #, $, %, ^, &, !, ?, _).',
            'password.not_regex' => 'Password must not contain spaces, backslashes, or forward slashes.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.max'       => 'Password must not exceed 64 characters.',
        ]);

        // Verify the current (temporary) password
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Prevent reusing the same password
        if (Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors(['password' => 'New password must be different from your current password.']);
        }

        // ── Update password (bcrypt via model mutator) ─────────────────────
        $user->update([
            'password'               => $request->input('password'),
            'password_reset_required'=> false,
        ]);

        AuditLog::record(
            AuditLog::PASSWORD_CHANGED,
            ['note' => 'Mandatory first-login password reset completed.'],
            $user->id,
            $user->full_name
        );

        return $this->redirectByRole($user)
            ->with('success', 'Password updated successfully. Welcome to EncryptEd.');
    }

    private function redirectByRole($user)
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

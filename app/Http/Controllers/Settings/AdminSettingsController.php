<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return view('settings.admin', ['user' => auth()->user()]);
    }

    public function updateSecurity(Request $request)
    {
        $user = auth()->user();

        $user->mergePreferences([
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'session_timeout'    => (int) $request->input('session_timeout', 60),
        ]);

        AuditLog::record('settings_updated', ['section' => 'security'], $user->id, $user->full_name);

        return back()->with('success_security', 'Security settings saved.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'          => ['required', 'string'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('tab', 'security');
        }

        $user->update([
            'password'               => $request->new_password,
            'password_reset_required' => false,
        ]);

        AuditLog::record('password_changed', null, $user->id, $user->full_name);

        return back()->with('success_password', 'Password updated successfully.');
    }
}

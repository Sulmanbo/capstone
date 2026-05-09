<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentSettingsController extends Controller
{
    public function index()
    {
        return view('settings.student', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
        ]);

        $user = auth()->user();
        $user->update($request->only('address', 'phone'));

        AuditLog::record('settings_updated', ['section' => 'profile'], $user->id, $user->full_name);

        return back()->with('success_profile', 'Profile information saved.');
    }

    public function updateEmergency(Request $request)
    {
        $request->validate([
            'emergency_name'         => ['nullable', 'string', 'max:100'],
            'emergency_phone'        => ['nullable', 'string', 'max:20'],
            'emergency_relationship' => ['nullable', 'string', 'max:50'],
        ]);

        $user = auth()->user();
        $user->mergePreferences([
            'emergency_contact_name'         => $request->input('emergency_name'),
            'emergency_contact_phone'        => $request->input('emergency_phone'),
            'emergency_contact_relationship' => $request->input('emergency_relationship'),
        ]);

        AuditLog::record('settings_updated', ['section' => 'emergency_contact'], $user->id, $user->full_name);

        return back()->with('success_emergency', 'Emergency contact saved.');
    }

    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        $user->mergePreferences([
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications'   => $request->boolean('sms_notifications'),
            'dark_mode'           => $request->boolean('dark_mode'),
        ]);

        AuditLog::record('settings_updated', ['section' => 'preferences'], $user->id, $user->full_name);

        return back()->with('success_prefs', 'Preferences saved.');
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
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password'               => $request->new_password,
            'password_reset_required' => false,
        ]);

        AuditLog::record('password_changed', null, $user->id, $user->full_name);

        return back()->with('success_password', 'Password updated successfully.');
    }
}

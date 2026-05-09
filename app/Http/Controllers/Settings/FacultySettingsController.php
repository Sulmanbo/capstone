<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FacultySettingsController extends Controller
{
    public function index()
    {
        return view('settings.faculty', ['user' => auth()->user()]);
    }

    public function updateContact(Request $request)
    {
        $request->validate([
            'phone'          => ['nullable', 'string', 'max:20'],
            'contact_method' => ['required', 'in:email,phone,both'],
        ]);

        $user = auth()->user();
        $user->update(['phone' => $request->phone]);
        $user->mergePreferences(['contact_method' => $request->contact_method]);

        AuditLog::record('settings_updated', ['section' => 'contact'], $user->id, $user->full_name);

        return back()->with('success_contact', 'Contact preferences saved.');
    }

    public function updateConsultation(Request $request)
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $hours = [];

        foreach ($days as $day) {
            $hours[$day] = [
                'available' => $request->boolean("days_{$day}"),
                'start'     => $request->input("start_{$day}", '08:00'),
                'end'       => $request->input("end_{$day}", '17:00'),
            ];
        }

        $user = auth()->user();
        $user->mergePreferences([
            'consultation_hours' => $hours,
            'advising_slots'     => (int) $request->input('advising_slots', 5),
        ]);

        AuditLog::record('settings_updated', ['section' => 'consultation'], $user->id, $user->full_name);

        return back()->with('success_consultation', 'Consultation hours saved.');
    }

    public function updateAlerts(Request $request)
    {
        $user = auth()->user();
        $user->mergePreferences([
            'grading_alerts'        => $request->boolean('grading_alerts'),
            'grading_alert_days'    => (int) $request->input('grading_alert_days', 3),
            'announcement_alerts'   => $request->boolean('announcement_alerts'),
        ]);

        AuditLog::record('settings_updated', ['section' => 'alerts'], $user->id, $user->full_name);

        return back()->with('success_alerts', 'Alert preferences saved.');
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

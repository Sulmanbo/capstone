<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrarSettingsController extends Controller
{
    public function index()
    {
        return view('settings.registrar', ['user' => auth()->user()]);
    }

    public function updateWorkflow(Request $request)
    {
        $request->validate([
            'notification_method'    => ['required', 'in:email,sms,both'],
            'enrollment_reminder'    => ['nullable', 'integer', 'min:1', 'max:30'],
            'document_routing'       => ['nullable', 'in:sequential,parallel'],
            'capacity_override'      => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();
        $user->mergePreferences([
            'notification_method' => $request->notification_method,
            'enrollment_reminder' => (int) $request->input('enrollment_reminder', 7),
            'document_routing'    => $request->input('document_routing', 'sequential'),
            'capacity_override'   => $request->boolean('capacity_override'),
        ]);

        AuditLog::record('settings_updated', ['section' => 'workflow'], $user->id, $user->full_name);

        return back()->with('success_workflow', 'Workflow settings saved.');
    }

    public function updateExport(Request $request)
    {
        $request->validate([
            'export_format'    => ['required', 'in:xlsx,csv,pdf'],
            'export_delimiter' => ['nullable', 'in:comma,semicolon,tab'],
            'include_headers'  => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();
        $user->mergePreferences([
            'export_format'    => $request->export_format,
            'export_delimiter' => $request->input('export_delimiter', 'comma'),
            'include_headers'  => $request->boolean('include_headers'),
        ]);

        AuditLog::record('settings_updated', ['section' => 'export'], $user->id, $user->full_name);

        return back()->with('success_export', 'Export defaults saved.');
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

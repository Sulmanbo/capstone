<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * UserManagementController
 *
 * Handles all user account CRUD for the Admin.
 * All PII is AES-256 encrypted via the User model mutators.
 * All passwords are bcrypt-hashed at cost 12.
 * All operations are logged to the audit trail.
 */
class UserManagementController extends Controller
{
    // ── List all users ─────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->input('role'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        // Search by username or LRN/employee number (plain text fields)
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('username', 'like', "%{$s}%")
                  ->orWhere('lrn', 'like', "%{$s}%")
                  ->orWhere('employee_number', 'like', "%{$s}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    // ── Show create form ───────────────────────────────────────────────────
    public function create()
    {
        return view('admin.users.create');
    }

    // ── Store new user account ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'gender'          => ['required', 'in:male,female'],
            'role_id'         => ['required', 'in:01,02,03,04'],
            'lrn'             => ['nullable', 'string', 'digits:9', 'unique:users,lrn'],
            'employee_number' => ['nullable', 'string', 'digits:9', 'unique:users,employee_number'],
        ]);

        // ── Generate institutional email ────────────────────────────────────
        $validated['email'] = $this->generateInstitutionalEmail($validated['first_name'], $validated['last_name']);

        // ── Validate email uniqueness via hash (encrypted email can't use unique rule directly) ──
        $emailHash = hash('sha256', strtolower(trim($validated['email'])));
        if (User::where('email_hash', $emailHash)->exists()) {
            return back()->withInput()->withErrors(['email' => 'Generated email address is already registered. Please adjust names or contact support.']);
        }

        // ── Generate role-based identifier if not provided ────────────────
        if ($validated['role_id'] === '01') {
            $validated['lrn'] = $validated['lrn'] ?? $this->generateStudentNumber();
        } else {
            $validated['employee_number'] = $validated['employee_number'] ?? $this->generateEmployeeNumber($validated['role_id']);
        }

        // ── Generate credentials ───────────────────────────────────────────
        $username        = $this->generateUsername($validated['first_name'], $validated['last_name'], $validated['role_id']);
        $tempPassword    = $this->generateTempPassword();

        // ── Create user (PII encrypted, password hashed via model mutators) ─
        $user = User::create([
            'first_name'             => $validated['first_name'],    // encrypted by mutator
            'last_name'              => $validated['last_name'],     // encrypted by mutator
            'email'                  => $validated['email'],         // encrypted + hash by mutator
            'username'               => $username,
            'password'               => $tempPassword,               // bcrypt by mutator
            'role_id'                => $validated['role_id'],
            'gender'                 => $validated['gender'],
            'lrn'                    => $validated['lrn']             ?? null,
            'employee_number'        => $validated['employee_number'] ?? null,
            'password_reset_required'=> true,
            'status'                 => 'active',
        ]);

        // ── Audit log ──────────────────────────────────────────────────────
        AuditLog::record(
            AuditLog::CREATE_USER,
            [
                'target_user_id'  => $user->id,
                'username'        => $username,
                'role_id'         => $validated['role_id'],
                'note'            => 'Account created by admin. Mandatory reset flagged.',
            ]
        );

        $assignedIdentifier = $user->lrn ?? $user->employee_number;
        $identifierLabel = $user->lrn ? 'Student Number' : 'Employee Number';

        // ── TODO: email the temp password to the user ──────────────────────
        // Mail::to($user->email)->send(new WelcomeCredentialsMail($user, $tempPassword));

        return redirect()->route('admin.users.index')
            ->with('success', "Account created. {$identifierLabel}: <strong>{$assignedIdentifier}</strong> — Email: <strong>{$validated['email']}</strong> — Username: <strong>{$username}</strong> — Temp password: <strong>{$tempPassword}</strong>. Share these credentials securely.");
    }

    // ── Show edit form ─────────────────────────────────────────────────────
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // ── Update user ────────────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email', 'max:200'],            'gender'          => ['required', 'in:male,female'],            'status'          => ['required', 'in:active,deactivated'],
            'role_id'         => ['required', 'in:01,02,03,04'],
        ]);

        // Check email uniqueness (excluding this user's own hash)
        $emailHash = hash('sha256', strtolower(trim($validated['email'])));
        if (User::where('email_hash', $emailHash)->where('id', '!=', $user->id)->exists()) {
            return back()->withInput()->withErrors(['email' => 'This email address is already in use.']);
        }

        $before = [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'gender'     => $user->gender,
            'status'     => $user->status,
            'role_id'    => $user->role_id,
        ];

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'gender'     => $validated['gender'],
            'status'     => $validated['status'],
            'role_id'    => $validated['role_id'],
        ]);

        // If deactivated, revoke any active sessions
        if ($validated['status'] === 'deactivated') {
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        AuditLog::record(
            AuditLog::UPDATE_USER,
            ['before' => $before, 'after' => $validated, 'target_user_id' => $user->id]
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User account updated successfully.');
    }

    // ── Toggle account status (AJAX-friendly) ─────────────────────────────
    public function toggleStatus(Request $request, User $user)
    {
        $newStatus = $user->status === 'active' ? 'deactivated' : 'active';

        $user->update(['status' => $newStatus]);

        if ($newStatus === 'deactivated') {
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        AuditLog::record(
            AuditLog::DEACTIVATE_USER,
            ['target_user_id' => $user->id, 'new_status' => $newStatus]
        );

        return redirect()->back()
            ->with('success', "Account {$newStatus} successfully.");
    }

    // ── Permanently delete a user account ─────────────────────────────────
    public function destroy(User $user)
    {
        // Prevent admin from deleting their own account
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $snapshot = [
            'deleted_user_id'       => $user->id,
            'deleted_username'      => $user->username,
            'deleted_role'          => $user->role_label,
            'deleted_employee_no'   => $user->employee_number,
            'deleted_lrn'           => $user->lrn,
            'note'                  => 'Permanent account deletion by admin.',
        ];

        // Revoke all active sessions first
        \DB::table('sessions')->where('user_id', $user->id)->delete();

        // Delete the user
        $user->delete();

        AuditLog::record(AuditLog::DELETE_RECORD, $snapshot);

        return redirect()->route('admin.users.index')
            ->with('success', "Account <strong>{$snapshot['deleted_username']}</strong> has been permanently deleted.");
    }

    // ── Admin resets another user's password ──────────────────────────────
    public function resetPassword(User $user)
    {
        $tempPassword = $this->generateTempPassword();

        $user->update([
            'password'               => $tempPassword,   // mutator handles bcrypt
            'password_reset_required'=> true,
            'failed_attempts'        => 0,
            'status'                 => 'active',
            'locked_until'           => null,
        ]);

        AuditLog::record(
            AuditLog::PASSWORD_RESET,
            ['target_user_id' => $user->id, 'note' => 'Admin-initiated password reset.']
        );

        return redirect()->back()
            ->with('success', "Password reset. New temp password: <strong>{$tempPassword}</strong>. Share securely.");
    }

    // ── Username generator ─────────────────────────────────────────────────
    private function generateUsername(string $firstName, string $lastName, string $roleId): string
    {
        $prefix = match($roleId) {
            '01' => 'stu',
            '02' => 'fac',
            '03' => 'reg',
            '04' => 'adm',
            default => 'usr',
        };

        $base     = strtolower($prefix . '.' . substr($firstName, 0, 1) . $lastName);
        $base     = preg_replace('/[^a-z0-9.]/', '', $base);
        $username = $base;
        $counter  = 1;

        // Ensure uniqueness
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    // ── Institutional email generator ──────────────────────────────────────
    private function generateInstitutionalEmail(string $firstName, string $lastName): string
    {
        $base = strtolower(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        $email = $base . '@pas.edu.ph';
        $counter = 1;

        // Ensure uniqueness
        while (User::where('email_hash', hash('sha256', $email))->exists()) {
            $email = $base . $counter . '@pas.edu.ph';
            $counter++;
        }

        return $email;
    }

    // ── Secure temp password generator ────────────────────────────────────
    private function generateTempPassword(): string
    {
        // Meets all password composition rules from the spec:
        // 8-64 chars, uppercase, lowercase, number, special char
        $upper   = strtoupper(Str::random(2));
        $lower   = strtolower(Str::random(4));
        $number  = random_int(10, 99);
        $special = ['@', '#', '$', '%', '^', '&', '!', '?', '_'][random_int(0, 8)];

        return str_shuffle($upper . $lower . $number . $special);
    }
    // ── Student number generator ───────────────────────────────────────
    private function generateStudentNumber(): string
    {
        $prefix = now()->format('Y');
        return $this->generateUniqueIdentifier($prefix, 5);
    }

    // ── Employee number generator for faculty/registrar/admin ───────────
    private function generateEmployeeNumber(string $roleId): string
    {
        $prefix = now()->format('Y');
        return $this->generateUniqueIdentifier($prefix, 5);
    }

    private function generateUniqueIdentifier(string $prefix, int $sequenceLength): string
    {
        $counter = 1;

        do {
            $candidate = $prefix . str_pad($counter, $sequenceLength, '0', STR_PAD_LEFT);
            $exists = User::where('lrn', $candidate)
                ->orWhere('employee_number', $candidate)
                ->exists();
            $counter++;
        } while ($exists);

        return $candidate;
    }
}

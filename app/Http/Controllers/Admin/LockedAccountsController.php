<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LockedAccountsController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters from query string ──────────────────────────────────────
        $search = $request->input('search');
        $role = $request->input('role');
        $sort = $request->input('sort', 'locked_until');
        $dir = $request->input('dir', 'desc');

        // ── Query ──────────────────────────────────────────────────────────
        $query = User::where('status', 'locked');

        // ── Search by name or email ────────────────────────────────────────
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ── Filter by role ─────────────────────────────────────────────────
        if ($role && in_array($role, ['01', '02', '03', '04'])) {
            $query->where('role_id', $role);
        }

        // ── Sorting ────────────────────────────────────────────────────────
        $query->orderBy($sort, $dir);

        // ── Pagination ────────────────────────────────────────────────────
        $locked_accounts = $query->paginate(50)->withQueryString();

        // ── Statistics ────────────────────────────────────────────────────
        $stats = [
            'total_locked' => User::where('status', 'locked')->count(),
            'locked_students' => User::where('status', 'locked')->where('role_id', '01')->count(),
            'locked_faculty' => User::where('status', 'locked')->where('role_id', '02')->count(),
            'locked_registrars' => User::where('status', 'locked')->where('role_id', '03')->count(),
            'locked_admins' => User::where('status', 'locked')->where('role_id', '04')->count(),
        ];

        return view('admin.security.locked-accounts', compact('locked_accounts', 'stats', 'search', 'role'));
    }

    public function unlock(User $user)
    {
        if ($user->status !== 'locked') {
            return redirect()->route('admin.locked-accounts.index')
                ->with('error', 'This account is not locked.');
        }

        $user->update([
            'status' => 'active',
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        return redirect()->route('admin.locked-accounts.index')
            ->with('success', "Account for {$user->username} has been unlocked.");
    }
}

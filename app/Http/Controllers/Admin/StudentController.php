<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters from query string ──────────────────────────────────────
        $search = $request->input('search');
        $gender = $request->input('gender'); // 'male', 'female', or null for all
        $address = $request->input('address');
        $sort = $request->input('sort', 'created_at');
        $dir = $request->input('dir', 'desc');

        // ── Query ──────────────────────────────────────────────────────────
        $query = User::where('role_id', '01')->where('status', 'active');

        // ── Search by name or email ────────────────────────────────────────
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%");
            });
        }

        // ── Filter by gender ───────────────────────────────────────────────
        if ($gender && in_array($gender, ['male', 'female'])) {
            $query->where('gender', $gender);
        }

        // ── Filter by address ──────────────────────────────────────────────
        if ($address) {
            $query->where('address', 'like', "%{$address}%");
        }

        // ── Sorting ────────────────────────────────────────────────────────
        $query->orderBy($sort, $dir);

        // ── Pagination ────────────────────────────────────────────────────
        $students = $query->paginate(50)->withQueryString();

        // ── Statistics ────────────────────────────────────────────────────
        $stats = [
            'total_students' => User::where('role_id', '01')->where('status', 'active')->count(),
            'male_students' => User::where('role_id', '01')->where('status', 'active')->where('gender', 'male')->count(),
            'female_students' => User::where('role_id', '01')->where('status', 'active')->where('gender', 'female')->count(),
        ];

        // ── Distinct addresses for filter dropdown ─────────────────────────
        $addresses = User::where('role_id', '01')
            ->where('status', 'active')
            ->whereNotNull('address')
            ->distinct()
            ->orderBy('address')
            ->pluck('address')
            ->values();

        return view('admin.students.index', compact('students', 'stats', 'addresses', 'search', 'gender', 'address'));
    }
}

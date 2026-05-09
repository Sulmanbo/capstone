<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters from query string ──────────────────────────────────────
        $search = $request->input('search');
        $gender = $request->input('gender');
        $sort = $request->input('sort', 'created_at');
        $dir = $request->input('dir', 'desc');

        // ── Query ──────────────────────────────────────────────────────────
        $query = User::where('role_id', '02')->where('status', 'active');

        // ── Search by name, email, or employee number ─────────────────────
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        // ── Filter by gender ───────────────────────────────────────────────
        if ($gender && in_array($gender, ['male', 'female'])) {
            $query->where('gender', $gender);
        }

        // ── Sorting ────────────────────────────────────────────────────────
        $query->orderBy($sort, $dir);

        // ── Pagination ────────────────────────────────────────────────────
        $faculty = $query->paginate(50)->withQueryString();

        // ── Statistics ────────────────────────────────────────────────────
        $stats = [
            'total_faculty' => User::where('role_id', '02')->where('status', 'active')->count(),
            'male_faculty' => User::where('role_id', '02')->where('status', 'active')->where('gender', 'male')->count(),
            'female_faculty' => User::where('role_id', '02')->where('status', 'active')->where('gender', 'female')->count(),
        ];

        return view('admin.faculty.index', compact('faculty', 'stats', 'search', 'gender'));
    }
}

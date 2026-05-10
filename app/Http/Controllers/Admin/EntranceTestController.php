<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\AuditLog;
use App\Models\EntranceTestResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EntranceTestController extends Controller
{
    private const DEFAULT_MAX   = 100.0;
    private const DEFAULT_PASS  = 75.0;

    // ── List all applicants with test status ────────────────────────────────

    public function index(Request $request): View
    {
        $query = Applicant::with(['entranceTestResult'])
            ->whereIn('status', ['under_review', 'accepted', 'rejected', 'enrolled'])
            ->latest();

        if ($request->filled('result')) {
            if ($request->input('result') === 'passed') {
                $query->whereHas('entranceTestResult', fn($q) => $q->where('passed', true));
            } elseif ($request->input('result') === 'failed') {
                $query->whereHas('entranceTestResult', fn($q) => $q->where('passed', false));
            } elseif ($request->input('result') === 'pending') {
                $query->whereDoesntHave('entranceTestResult');
            }
        }

        if ($request->filled('grade')) {
            $query->where('applying_for_grade', $request->input('grade'));
        }

        $applicants = $query->paginate(25)->withQueryString();
        $grades     = Applicant::distinct()->orderBy('applying_for_grade')->pluck('applying_for_grade');

        $counts = [
            'total'   => Applicant::whereIn('status', ['under_review', 'accepted', 'rejected', 'enrolled'])->count(),
            'tested'  => EntranceTestResult::count(),
            'passed'  => EntranceTestResult::where('passed', true)->count(),
            'failed'  => EntranceTestResult::where('passed', false)->count(),
        ];

        return view('admin.entrance-tests.index', compact('applicants', 'grades', 'counts'));
    }

    // ── Record / edit test for an applicant ────────────────────────────────

    public function create(Applicant $applicant): View
    {
        $result    = $applicant->entranceTestResult;
        $testAreas = EntranceTestResult::TEST_AREAS;
        return view('admin.entrance-tests.form', compact('applicant', 'result', 'testAreas'));
    }

    public function store(Request $request, Applicant $applicant): RedirectResponse
    {
        $areaKeys  = array_keys(EntranceTestResult::TEST_AREAS);
        $validated = $this->validateRequest($request, $areaKeys);

        // Build per-area scores array
        $scores = [];
        foreach ($areaKeys as $key) {
            if (isset($validated["score_{$key}"])) {
                $scores[$key] = (float) $validated["score_{$key}"];
            }
        }

        $total   = (float) $validated['total_score'];
        $max     = (float) ($validated['max_score']     ?? self::DEFAULT_MAX);
        $passing = (float) ($validated['passing_score'] ?? self::DEFAULT_PASS);
        $passed  = $total >= $passing;

        $existing = $applicant->entranceTestResult;

        if ($existing) {
            $existing->update([
                'test_date'       => $validated['test_date'],
                'administered_by' => $validated['administered_by'] ?? null,
                'scores'          => $scores ?: null,
                'total_score'     => $total,
                'max_score'       => $max,
                'passing_score'   => $passing,
                'passed'          => $passed,
                'notes'           => $validated['notes'] ?? null,
            ]);
            $result = $existing;
        } else {
            $result = EntranceTestResult::create([
                'applicant_id'    => $applicant->id,
                'test_date'       => $validated['test_date'],
                'administered_by' => $validated['administered_by'] ?? null,
                'scores'          => $scores ?: null,
                'total_score'     => $total,
                'max_score'       => $max,
                'passing_score'   => $passing,
                'passed'          => $passed,
                'notes'           => $validated['notes'] ?? null,
            ]);
        }

        AuditLog::record(AuditLog::ENTRANCE_TEST_RECORDED, [
            'applicant_id'     => $applicant->id,
            'reference_number' => $applicant->reference_number,
            'total_score'      => $total,
            'passed'           => $passed,
        ]);

        return redirect()
            ->route('admin.applicants.show', $applicant->id)
            ->with('success', 'Entrance test result saved. Applicant ' . ($passed ? 'PASSED' : 'did not pass') . '.');
    }

    // ── Private ─────────────────────────────────────────────────────────────

    private function validateRequest(Request $request, array $areaKeys): array
    {
        $rules = [
            'test_date'       => ['required', 'date'],
            'administered_by' => ['nullable', 'exists:users,id'],
            'total_score'     => ['required', 'numeric', 'min:0', 'max:9999'],
            'max_score'       => ['nullable', 'numeric', 'min:1', 'max:9999'],
            'passing_score'   => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ];

        foreach ($areaKeys as $key) {
            $rules["score_{$key}"] = ['nullable', 'numeric', 'min:0', 'max:9999'];
        }

        return $request->validate($rules);
    }
}

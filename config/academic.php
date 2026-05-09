<?php

/**
 * Academic Configuration
 *
 * DepEd K-12 grade computation weights (DepEd Order No. 8 s. 2015):
 *   Written Work       — 30 %
 *   Performance Task   — 50 %
 *   Quarterly Assessment — 20 %
 *
 * Adjust weights here; Grade::computeFinalGrade() reads them at runtime.
 */
return [

    // ── DepEd grade component weights (must sum to 1.0) ───────────────────
    'grade_weights' => [
        'written_work'          => 0.30,
        'performance_task'      => 0.50,
        'quarterly_assessment'  => 0.20,
    ],

    // ── Minimum passing grade (DepEd: 75) ─────────────────────────────────
    'passing_grade' => 75,

    // ── Grade descriptors (DepEd table) ───────────────────────────────────
    'descriptors' => [
        ['min' => 90, 'max' => 100, 'label' => 'Outstanding',          'remarks' => 'Passed'],
        ['min' => 85, 'max' => 89,  'label' => 'Very Satisfactory',    'remarks' => 'Passed'],
        ['min' => 80, 'max' => 84,  'label' => 'Satisfactory',         'remarks' => 'Passed'],
        ['min' => 75, 'max' => 79,  'label' => 'Fairly Satisfactory',  'remarks' => 'Passed'],
        ['min' => 0,  'max' => 74,  'label' => 'Did Not Meet Expectations', 'remarks' => 'Failed'],
    ],

    // ── Grade workflow statuses (in order) ────────────────────────────────
    'grade_statuses' => ['draft', 'submitted', 'finalized', 'locked'],

    // ── Audit log retention in years (used by audit:prune command) ────────
    'audit_retention_years' => env('AUDIT_RETENTION_YEARS', 2),
];

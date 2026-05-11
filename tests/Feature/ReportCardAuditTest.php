<?php

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\ReportCardToken;
use App\Models\User;

it('logs REPORT_CARD_GENERATED on download', function () {
    $student = User::factory()->student()->create();
    $year    = AcademicYear::create([
        'year_label' => '2025-2026',
        'status'     => 'active',
        'start_date' => '2025-06-01',
        'end_date'   => '2026-03-31',
    ]);

    $this->actingAs($student)->get(route('report-card.download', $student->id));

    expect(AuditLog::where('action_type', 'REPORT_CARD_GENERATED')->exists())->toBeTrue();
});

it('logs REPORT_CARD_VERIFIED on public verify', function () {
    $student = User::factory()->student()->create();
    $year    = AcademicYear::create([
        'year_label' => '2025-2026-verify',
        'status'     => 'active',
        'start_date' => '2025-06-01',
        'end_date'   => '2026-03-31',
    ]);
    $tokenValue = 'test-token-' . uniqid();
    $token      = ReportCardToken::create([
        'student_id'       => $student->id,
        'academic_year_id' => $year->id,
        'quarter_number'   => null,
        'token'            => $tokenValue,
        'data_hash'        => hash('sha256', '[]'),
        'generated_by'     => $student->id,
    ]);

    $this->get(route('report-card.verify', $token->token));

    expect(AuditLog::where('action_type', 'REPORT_CARD_VERIFIED')
        ->where('data_payload', 'like', '%' . $tokenValue . '%')
        ->exists())->toBeTrue();
});

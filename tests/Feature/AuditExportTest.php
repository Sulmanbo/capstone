<?php

use App\Models\AuditLog;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('admin can export audit log as PDF', function () {
    // Seed a few audit rows
    AuditLog::record(AuditLog::LOGIN_SUCCESS, ['username' => 'someone']);
    AuditLog::record(AuditLog::GRADE_SUBMITTED, ['section_subject_id' => 1]);

    $response = $this->actingAs($this->admin)->get(route('admin.audit.export-pdf', [
        'date_from' => now()->subDay()->toDateString(),
        'date_to'   => now()->toDateString(),
    ]));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('audit export action is itself audited', function () {
    $this->actingAs($this->admin)->get(route('admin.audit.export-pdf'));

    expect(AuditLog::where('action_type', AuditLog::AUDIT_LOG_EXPORTED)->exists())->toBeTrue();
});

test('non-admin cannot export audit log', function () {
    $student = User::factory()->student()->create();

    $this->actingAs($student)
         ->get(route('admin.audit.export-pdf'))
         ->assertStatus(403);
});

test('export validates date range correctness', function () {
    $this->actingAs($this->admin)
         ->get(route('admin.audit.export-pdf', [
             'date_from' => '2026-12-01',
             'date_to'   => '2026-01-01',   // before date_from
         ]))
         ->assertSessionHasErrors('date_to');
});

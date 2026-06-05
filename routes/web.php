<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForcePasswordResetController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ThreatController;
use App\Http\Controllers\Admin\ComplianceController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\RegistrarController;
use App\Http\Controllers\Admin\LockedAccountsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\RegistrarDashboardController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\GradingQuarterController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\CurriculumMappingController;
use App\Http\Controllers\Admin\EntranceTestController;
use App\Http\Controllers\Settings\AdminSettingsController;
use App\Http\Controllers\Settings\StudentSettingsController;
use App\Http\Controllers\Settings\FacultySettingsController;
use App\Http\Controllers\Settings\RegistrarSettingsController;

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// ── Guest Auth Routes ─────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {

    // Login
    Route::get( '/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');

    // ── Password Recovery (3-step OTP flow) ──────────────────────────────
    // Step 1 — Enter email
    Route::get( '/forgot-password',        [PasswordRecoveryController::class, 'showEmailForm'])->name('password.request');
    Route::post('/forgot-password',        [PasswordRecoveryController::class, 'sendOtp'])->name('password.email')->middleware('throttle:5,1');

    // Step 2 — Enter OTP
    Route::get( '/forgot-password/verify', [PasswordRecoveryController::class, 'showVerifyForm'])->name('password.verify-otp');
    Route::post('/forgot-password/verify', [PasswordRecoveryController::class, 'verifyOtp'])->name('password.verify-otp.submit')->middleware('throttle:10,1');

    // Step 3 — Set new password
    Route::get( '/forgot-password/reset',  [PasswordRecoveryController::class, 'showResetForm'])->name('password.reset-form');
    Route::post('/forgot-password/reset',  [PasswordRecoveryController::class, 'resetPassword'])->name('password.do-reset')->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── Report Card (authenticated download + public verify) ──────────────────
Route::get('/report-card/{student}/download', [\App\Http\Controllers\ReportCardController::class, 'download'])
    ->middleware('auth')
    ->name('report-card.download');

Route::get('/verify/{token}', [\App\Http\Controllers\ReportCardController::class, 'verify'])
    ->name('report-card.verify');

// ── Public Applicant Onboarding — REMOVED (out of scope: grading management only) ──

// ── Global Academic-Year Context (header selector, all roles) ──────────────
Route::post('/academic-year/switch', [\App\Http\Controllers\AcademicYearContextController::class, 'switch'])
    ->middleware('auth')
    ->name('academic-year.switch');

// ── Mandatory First-Login Password Reset ──────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get( '/password/reset-required', [ForcePasswordResetController::class, 'show'])  ->name('password.force-reset');
    Route::post('/password/reset-required', [ForcePasswordResetController::class, 'update'])->name('password.force-reset.update');
});

// ── Admin Routes ──────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // ── Payments & Enrollment Fees (pay-first policy) ────────────────────
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/',                      [\App\Http\Controllers\Admin\PaymentController::class, 'index'])   ->name('index');
        Route::post('/{payment}/confirm',    [\App\Http\Controllers\Admin\PaymentController::class, 'confirm']) ->name('confirm');
        Route::post('/{payment}/reject',     [\App\Http\Controllers\Admin\PaymentController::class, 'reject'])  ->name('reject');
        Route::get('/fees',                  [\App\Http\Controllers\Admin\PaymentController::class, 'fees'])    ->name('fees');
        Route::post('/fees',                 [\App\Http\Controllers\Admin\PaymentController::class, 'storeFee'])->name('fees.store');
    });

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/security-settings', [\App\Http\Controllers\Settings\AdminSettingsController::class, 'index'])->name('security-settings');
    Route::get('/grades', [\App\Http\Controllers\Admin\AdminGradesController::class, 'index'])->name('grades.index');

    // ── User Management ───────────────────────────────────────────────────
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',                       [UserManagementController::class, 'index'])        ->name('index');
        Route::get('/create',                 [UserManagementController::class, 'create'])       ->name('create');
        Route::post('/',                      [UserManagementController::class, 'store'])        ->name('store');
        Route::get('/{user}/edit',            [UserManagementController::class, 'edit'])         ->name('edit');
        Route::put('/{user}',                 [UserManagementController::class, 'update'])       ->name('update');
        Route::delete('/{user}',              [UserManagementController::class, 'destroy'])      ->name('destroy');
        Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('reset-password');
    });

    // ── Students Management ───────────────────────────────────────────────
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/',          [StudentController::class,       'index'])    ->name('index');
        Route::get('/import',    [\App\Http\Controllers\Admin\StudentImportController::class, 'showForm'])->name('import');
        Route::post('/import',   [\App\Http\Controllers\Admin\StudentImportController::class, 'import'])  ->name('import.submit');
        Route::get('/import/template', function () {
            $csv = implode("\n", [
                'first_name,last_name,email,lrn,grade_level,section_name,gender,phone,address',
                'Juan,Dela Cruz,juan@example.com,123456789012,7,Section A,male,09171234567,Manila',
                'Maria,Santos,maria@example.com,123456789013,8,Section B,female,,',
            ]);
            return response($csv, 200, [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="student_import_template.csv"',
            ]);
        })->name('import.template');
    });

    // ── Faculty Management ────────────────────────────────────────────────
    Route::prefix('faculty')->name('faculty.')->group(function () {
        Route::get('/', [FacultyController::class, 'index'])->name('index');
    });

    // ── Registrars Management ─────────────────────────────────────────────
    Route::prefix('registrars')->name('registrars.')->group(function () {
        Route::get('/', [RegistrarController::class, 'index'])->name('index');
    });

    // ── Registrar Module — Dashboard ──────────────────────────────────────
    Route::get('/registrar-dashboard', [RegistrarDashboardController::class, 'index'])->name('registrar-dashboard');

    // ── Applicant Management — REMOVED (out of scope: grading management only) ──
    // ── Entrance Test Results   — REMOVED (out of scope: grading management only) ──

    // ── Academic Years Management ─────────────────────────────────────────
    Route::prefix('academic-years')->name('academic-years.')->group(function () {
        Route::get('/',                [AcademicYearController::class, 'index'])   ->name('index');
        Route::get('/create',          [AcademicYearController::class, 'create'])  ->name('create');
        Route::post('/',               [AcademicYearController::class, 'store'])   ->name('store');
        Route::get('/{academicYear}/edit', [AcademicYearController::class, 'edit']) ->name('edit');
        Route::put('/{academicYear}',  [AcademicYearController::class, 'update'])  ->name('update');
        Route::patch('/{academicYear}/toggle', [AcademicYearController::class, 'toggle'])->name('toggle');
        Route::delete('/{academicYear}', [AcademicYearController::class, 'destroy'])->name('destroy');
    });

    // ── Classrooms Management (FRS §Classroom Module per adviser) ────────
    Route::prefix('classrooms')->name('classrooms.')->group(function () {
        Route::get('/',                        [\App\Http\Controllers\Admin\ClassroomController::class, 'index'])  ->name('index');
        Route::post('/',                       [\App\Http\Controllers\Admin\ClassroomController::class, 'store'])  ->name('store');
        Route::put('/{classroom}',             [\App\Http\Controllers\Admin\ClassroomController::class, 'update']) ->name('update');
        Route::delete('/{classroom}',          [\App\Http\Controllers\Admin\ClassroomController::class, 'destroy'])->name('destroy');
    });

    // ── Sections Management ──────────────────────────────────────────────
    Route::prefix('sections')->name('sections.')->group(function () {
        Route::get('/',                  [\App\Http\Controllers\Admin\SectionController::class, 'index'])  ->name('index');
        Route::post('/',                 [\App\Http\Controllers\Admin\SectionController::class, 'store'])  ->name('store');
        Route::put('/{section}',         [\App\Http\Controllers\Admin\SectionController::class, 'update']) ->name('update');
        Route::delete('/{section}',      [\App\Http\Controllers\Admin\SectionController::class, 'destroy'])->name('destroy');
        Route::get('/{section}/roster',  [\App\Http\Controllers\Admin\SectionController::class, 'roster']) ->name('roster');
        Route::post('/{section}/enroll', [\App\Http\Controllers\Admin\SectionController::class, 'enrollStudents'])->name('enroll');
        Route::delete('/{section}/remove-student', [\App\Http\Controllers\Admin\SectionController::class, 'removeStudent'])->name('remove-student');
    });

    // ── Grading Quarters Management ───────────────────────────────────────
    Route::prefix('grading-quarters')->name('grading-quarters.')->group(function () {
        Route::get('/',                [GradingQuarterController::class, 'index'])   ->name('index');
        Route::get('/create',          [GradingQuarterController::class, 'create'])  ->name('create');
        Route::post('/',               [GradingQuarterController::class, 'store'])   ->name('store');
        Route::get('/{quarter}/edit',  [GradingQuarterController::class, 'edit'])    ->name('edit');
        Route::put('/{quarter}',       [GradingQuarterController::class, 'update'])  ->name('update');
        Route::delete('/{quarter}',    [GradingQuarterController::class, 'destroy']) ->name('destroy');
    });

    // ── Subjects Registry Management ──────────────────────────────────────
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/',                [SubjectController::class, 'index'])        ->name('index');
        Route::get('/create',          [SubjectController::class, 'create'])       ->name('create');
        Route::post('/',               [SubjectController::class, 'store'])        ->name('store');
        Route::get('/{subject}',       [SubjectController::class, 'show'])         ->name('show');
        Route::get('/{subject}/edit',  [SubjectController::class, 'edit'])         ->name('edit');
        Route::put('/{subject}',       [SubjectController::class, 'update'])       ->name('update');
        Route::delete('/{subject}',    [SubjectController::class, 'destroy'])      ->name('destroy');
    });

    // ── Curriculum Mapping Management ─────────────────────────────────────
    Route::prefix('curriculum-mappings')->name('curriculum-mappings.')->group(function () {
        Route::get('/',                        [CurriculumMappingController::class, 'index'])        ->name('index');
        Route::get('/create',                  [CurriculumMappingController::class, 'create'])       ->name('create');
        Route::post('/',                       [CurriculumMappingController::class, 'store'])        ->name('store');
        Route::get('/{mapping}/edit',          [CurriculumMappingController::class, 'edit'])         ->name('edit');
        Route::put('/{mapping}',               [CurriculumMappingController::class, 'update'])       ->name('update');
        Route::delete('/{mapping}',            [CurriculumMappingController::class, 'destroy'])      ->name('destroy');
        Route::post('/bulk-action',            [CurriculumMappingController::class, 'bulkAction'])   ->name('bulk-action');
    });

    // ── Locked Accounts Management ────────────────────────────────────────
    Route::prefix('locked-accounts')->name('locked-accounts.')->group(function () {
        Route::get('/', [LockedAccountsController::class, 'index'])->name('index');
        Route::patch('/{user}/unlock', [LockedAccountsController::class, 'unlock'])->name('unlock');
    });

    // ── Threat Monitoring & Audit ─────────────────────────────────────────
    Route::get('/audit',             [AuditLogController::class,  'index'])    ->name('audit.index');
    Route::get('/audit/export.pdf',  [AuditLogController::class,  'exportPdf'])->name('audit.export-pdf');
    Route::get('/threats',           [ThreatController::class,    'index']) ->name('threat.index');
    Route::get('/compliance',        [ComplianceController::class, 'index'])->name('compliance.index');
    Route::get('/compliance/export', [ComplianceController::class, 'export'])->name('compliance.export');

    // ── Announcements ─────────────────────────────────────────────────────
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/',                           [\App\Http\Controllers\Admin\AnnouncementController::class, 'index'])  ->name('index');
        Route::post('/',                          [\App\Http\Controllers\Admin\AnnouncementController::class, 'store'])  ->name('store');
        Route::delete('/{announcement}',          [\App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('destroy');
        Route::patch('/{announcement}/toggle',    [\App\Http\Controllers\Admin\AnnouncementController::class, 'toggle']) ->name('toggle');
    });

    // ── Schedule Management (formerly Faculty Schedules; per adviser feedback) ──
    // Schedules are created first; faculty assignment is the last step and may
    // be left as "TBA". Uses cascading dropdowns rather than free-text fields.
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/',                                  [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])     ->name('index');
        Route::get('/create',                            [\App\Http\Controllers\Admin\ScheduleController::class, 'create'])    ->name('create');
        Route::post('/',                                 [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])     ->name('store');
        Route::get('/{schedule}/edit',                   [\App\Http\Controllers\Admin\ScheduleController::class, 'edit'])      ->name('edit');
        Route::put('/{schedule}',                        [\App\Http\Controllers\Admin\ScheduleController::class, 'update'])    ->name('update');
        Route::delete('/{schedule}',                     [\App\Http\Controllers\Admin\ScheduleController::class, 'destroy'])   ->name('destroy');
        Route::post('/{schedule}/assign-faculty',        [\App\Http\Controllers\Admin\ScheduleController::class, 'assignFaculty'])->name('assign-faculty');
        // AJAX endpoint for cascading subject dropdown
        Route::get('/subjects-for-section/{section}',    [\App\Http\Controllers\Admin\ScheduleController::class, 'subjectsForSection'])->name('subjects-for-section');
        Route::post('/check-conflict',                   [\App\Http\Controllers\Admin\ScheduleController::class, 'checkConflict'])->name('check-conflict');
    });

    // ── Admin Settings ────────────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',          [AdminSettingsController::class, 'index'])          ->name('index');
        Route::post('/security', [AdminSettingsController::class, 'updateSecurity']) ->name('security');
        Route::post('/password', [AdminSettingsController::class, 'updatePassword']) ->name('password');
    });
});

// ── Notifications ─────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications',              [\App\Http\Controllers\NotificationController::class, 'index'])           ->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])       ->name('notifications.unread-count');
    Route::post('/notifications/{notification}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markRead'])   ->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])     ->name('notifications.mark-all-read');
});

// ── Role-Specific Dashboard Routes ────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Registrar Dashboard & Pages
    Route::middleware('role:registrar')->group(function () {
        Route::get('/registrar/dashboard',      [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'index'])        ->name('registrar.dashboard');
        Route::get('/registrar/students',       [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'students'])      ->name('registrar.students');
        Route::get('/registrar/enrollment',     [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'enrollment'])    ->name('registrar.enrollment');
        Route::get('/registrar/requests',       [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'requests'])      ->name('registrar.requests');
        Route::get('/registrar/report-cards',   [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'reportCards'])   ->name('registrar.report-cards');
        Route::get('/registrar/grades',         [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'grades'])        ->name('registrar.grades');
        Route::get('/registrar/calendar',       [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'calendar'])      ->name('registrar.calendar');
        Route::get('/registrar/announcements',  [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'announcements']) ->name('registrar.announcements');
        Route::post('/registrar/announcements', [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'postAnnouncement'])->name('registrar.announcements.store');

        // Enrollment (with prerequisite enforcement)
        Route::post('/registrar/enroll',          [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'enroll'])->name('registrar.enroll');
        Route::post('/registrar/drop-enrollment', [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'dropEnrollment'])->name('registrar.drop-enrollment');
        // AJAX helpers for cascading dropdowns
        Route::get('/registrar/ajax/sections',    [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'ajaxSections'])->name('registrar.ajax.sections');
        Route::get('/registrar/ajax/students',    [App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'ajaxStudents'])->name('registrar.ajax.students');
        Route::get('/registrar/ajax/section-info',[App\Http\Controllers\Dashboard\RegistrarUserDashboardController::class, 'ajaxSectionInfo'])->name('registrar.ajax.section-info');

        // Grade finalization
        Route::post('/registrar/gradebook/{sectionSubject}/finalize', [App\Http\Controllers\Dashboard\GradebookController::class, 'finalize'])->name('registrar.gradebook.finalize');

        // Grade verification & finalization
        Route::post('/registrar/grades/{grade}/finalize', [App\Http\Controllers\Dashboard\GradeVerificationController::class, 'finalize'])->name('registrar.grades.finalize');
        Route::post('/registrar/grades/{grade}/lock',     [App\Http\Controllers\Dashboard\GradeVerificationController::class, 'lock'])    ->name('registrar.grades.lock');
        Route::post('/registrar/grades/{grade}/unlock',   [App\Http\Controllers\Dashboard\GradeVerificationController::class, 'unlock'])  ->name('registrar.grades.unlock');
        Route::post('/registrar/grades/finalize-bulk',    [App\Http\Controllers\Dashboard\GradeVerificationController::class, 'bulkFinalize'])->name('registrar.grades.bulk-finalize');

        // Grade lock management
        Route::get( '/registrar/grade-lock',                                       [App\Http\Controllers\Admin\GradeLockController::class, 'index'])         ->name('registrar.grade-lock.index');
        Route::post('/registrar/grade-lock/{sectionSubject}/lock',                 [App\Http\Controllers\Admin\GradeLockController::class, 'lockSection'])   ->name('registrar.grade-lock.lock-section');
        Route::post('/registrar/grade-lock/lock-all',                              [App\Http\Controllers\Admin\GradeLockController::class, 'lockAll'])        ->name('registrar.grade-lock.lock-all');
        Route::post('/registrar/grade-lock/unlock-requests/{unlockRequest}/approve', [App\Http\Controllers\Admin\GradeLockController::class, 'approveUnlock'])->name('registrar.grade-lock.approve');
        Route::post('/registrar/grade-lock/unlock-requests/{unlockRequest}/deny',    [App\Http\Controllers\Admin\GradeLockController::class, 'denyUnlock'])   ->name('registrar.grade-lock.deny');

        // Student Promotion and Advancement
        Route::get( '/registrar/promotion', [App\Http\Controllers\Dashboard\PromotionController::class, 'index'])   ->name('registrar.promotion');
        Route::post('/registrar/promotion', [App\Http\Controllers\Dashboard\PromotionController::class, 'promote']) ->name('registrar.promotion.promote');

        // Aggregate Reports (honor roll + academic intervention)
        Route::get('/registrar/reports/aggregate', [App\Http\Controllers\Dashboard\AggregateReportController::class, 'index'])->name('registrar.reports.aggregate');
    });

    // Faculty Dashboard & Pages
    Route::middleware('role:faculty')->group(function () {
        Route::get('/faculty/dashboard',      [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'index'])       ->name('faculty.dashboard');
        Route::get('/faculty/my-classes',     [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'myClasses'])   ->name('faculty.classes');
        Route::get('/faculty/gradebook',      [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'gradebook'])   ->name('faculty.gradebook');
        Route::get('/faculty/attendance',     [App\Http\Controllers\Dashboard\AttendanceController::class,        'index'])  ->name('faculty.attendance');
        Route::post('/faculty/attendance',    [App\Http\Controllers\Dashboard\AttendanceController::class,        'store'])  ->name('faculty.attendance.store');
        Route::get('/faculty/my-schedule',    [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'mySchedule'])  ->name('faculty.my-schedule');
        Route::get('/faculty/announcements',  [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'announcements'])->name('faculty.announcements');
        Route::post('/faculty/announcements', [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'postAnnouncement'])->name('faculty.announcements.store');

        // Grade entry workflow
        Route::get( '/faculty/gradebook/{sectionSubject}',                     [App\Http\Controllers\Dashboard\GradebookController::class, 'show'])          ->name('faculty.gradebook.show');
        Route::post('/faculty/gradebook/{sectionSubject}/save-draft',          [App\Http\Controllers\Dashboard\GradebookController::class, 'saveDraft'])      ->name('faculty.gradebook.save-draft');
        Route::post('/faculty/gradebook/{sectionSubject}/submit',              [App\Http\Controllers\Dashboard\GradebookController::class, 'submit'])         ->name('faculty.gradebook.submit');
        Route::post('/faculty/gradebook/{sectionSubject}/request-unlock',      [App\Http\Controllers\Dashboard\GradebookController::class, 'requestUnlock'])  ->name('faculty.gradebook.request-unlock');

        // Dropped student workflow
        Route::post('/faculty/gradebook/{sectionSubject}/drop',      [App\Http\Controllers\Dashboard\GradebookController::class, 'dropStudent'])      ->name('faculty.gradebook.drop');
        Route::post('/faculty/gradebook/{sectionSubject}/reinstate',  [App\Http\Controllers\Dashboard\GradebookController::class, 'reinstateStudent']) ->name('faculty.gradebook.reinstate');

        // Faculty Inbox / Messaging
        Route::get( '/faculty/inbox',                  [App\Http\Controllers\Dashboard\MessageController::class, 'facultyInbox']) ->name('faculty.inbox');
        Route::get( '/faculty/inbox/{message}',        [App\Http\Controllers\Dashboard\MessageController::class, 'facultyShow'])  ->name('faculty.inbox.show');
        Route::post('/faculty/inbox/{message}/reply',  [App\Http\Controllers\Dashboard\MessageController::class, 'facultyReply']) ->name('faculty.inbox.reply');
    });

    // Student Dashboard
    Route::get('/student/dashboard', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'index'])
        ->middleware('role:student')
        ->name('student.dashboard');

    Route::get('/student/report-card', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'reportCard'])
        ->middleware('role:student')
        ->name('student.report-card');

    Route::get('/student/grade-archive', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'gradeArchive'])
        ->middleware('role:student')
        ->name('student.grade-archive');

    // ── Student Payments (pay-first enrollment) ──────────────────────────
    Route::middleware('role:student')->group(function () {
        Route::get( '/student/payments',        [App\Http\Controllers\Dashboard\PaymentController::class, 'index'])  ->name('student.payments.index');
        Route::post('/student/payments',        [App\Http\Controllers\Dashboard\PaymentController::class, 'submit']) ->name('student.payments.submit');
    });

    Route::get('/student/academic-holds', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'academicHolds'])
        ->middleware('role:student')
        ->name('student.academic-holds');

    Route::get('/student/account-balance', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'accountBalance'])
        ->middleware('role:student')
        ->name('student.account-balance');

    Route::get('/student/admission-documents', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'admissionDocuments'])
        ->middleware('role:student')
        ->name('student.admission-documents');

    Route::get('/student/course-offerings', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'courseOfferings'])
        ->middleware('role:student')
        ->name('student.course-offerings');

    Route::get('/student/program-curriculum', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'programCurriculum'])
        ->middleware('role:student')
        ->name('student.program-curriculum');

    Route::get('/student/schedule', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'schedule'])
        ->middleware('role:student')
        ->name('student.schedule');

    Route::get('/student/attendance', [App\Http\Controllers\Dashboard\StudentAttendanceController::class, 'index'])
        ->middleware('role:student')
        ->name('student.attendance');

    // ── Student Inbox / Messaging ─────────────────────────────────────────
    Route::middleware('role:student')->prefix('student/inbox')->name('student.inbox')->group(function () {
        Route::get('/',            [App\Http\Controllers\Dashboard\MessageController::class, 'studentInbox']) ->name('');
        Route::post('/',           [App\Http\Controllers\Dashboard\MessageController::class, 'studentStore']) ->name('.store');
        Route::get('/{message}',   [App\Http\Controllers\Dashboard\MessageController::class, 'studentShow'])  ->name('.show');
    });

    // ── Student Settings ──────────────────────────────────────────────────
    Route::middleware('role:student')->prefix('student/settings')->name('student.settings.')->group(function () {
        Route::get('/',          [StudentSettingsController::class, 'index'])             ->name('index');
        Route::post('/profile',  [StudentSettingsController::class, 'updateProfile'])     ->name('profile');
        Route::post('/emergency',[StudentSettingsController::class, 'updateEmergency'])   ->name('emergency');
        Route::post('/prefs',    [StudentSettingsController::class, 'updatePreferences']) ->name('preferences');
        Route::post('/password', [StudentSettingsController::class, 'updatePassword'])    ->name('password');
    });

    // ── Faculty Settings ──────────────────────────────────────────────────
    Route::middleware('role:faculty')->prefix('faculty/settings')->name('faculty.settings.')->group(function () {
        Route::get('/',              [FacultySettingsController::class, 'index'])               ->name('index');
        Route::post('/contact',      [FacultySettingsController::class, 'updateContact'])       ->name('contact');
        Route::post('/consultation', [FacultySettingsController::class, 'updateConsultation'])  ->name('consultation');
        Route::post('/alerts',       [FacultySettingsController::class, 'updateAlerts'])        ->name('alerts');
        Route::post('/password',     [FacultySettingsController::class, 'updatePassword'])      ->name('password');
    });

    // ── Registrar Settings ────────────────────────────────────────────────
    Route::middleware('role:registrar')->prefix('registrar/settings')->name('registrar.settings.')->group(function () {
        Route::get('/',          [RegistrarSettingsController::class, 'index'])            ->name('index');
        Route::post('/workflow', [RegistrarSettingsController::class, 'updateWorkflow'])   ->name('workflow');
        Route::post('/export',   [RegistrarSettingsController::class, 'updateExport'])     ->name('export');
        Route::post('/password', [RegistrarSettingsController::class, 'updatePassword'])   ->name('password');
    });

    // ── Grade Complaints ──────────────────────────────────────────────────
    // Student: submit and list own complaints
    Route::middleware('role:student')->group(function () {
        Route::get( '/complaints',        [\App\Http\Controllers\GradeComplaintController::class, 'index'])  ->name('complaints.index');
        Route::get( '/complaints/create', [\App\Http\Controllers\GradeComplaintController::class, 'create']) ->name('complaints.create');
        Route::post('/complaints',        [\App\Http\Controllers\GradeComplaintController::class, 'store'])  ->name('complaints.store');
    });
    // Faculty / Registrar / Admin: review and respond
    Route::middleware('role:faculty,registrar,admin')->group(function () {
        Route::get(  '/complaints/manage',              [\App\Http\Controllers\GradeComplaintController::class, 'manage'])  ->name('complaints.manage');
        Route::patch('/complaints/{complaint}/respond', [\App\Http\Controllers\GradeComplaintController::class, 'respond']) ->name('complaints.respond');
    });

    // ── School Calendar ───────────────────────────────────────────────────
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar', [\App\Http\Controllers\CalendarController::class, 'store'])->name('calendar.store')->middleware('role:registrar,admin');
    Route::delete('/calendar/{calendarEvent}', [\App\Http\Controllers\CalendarController::class, 'destroy'])->name('calendar.destroy')->middleware('role:registrar,admin');

    // ── Document Requests ─────────────────────────────────────────────────
    Route::middleware('role:student')->group(function () {
        Route::get( '/documents',       [\App\Http\Controllers\DocumentRequestController::class, 'studentIndex'])->name('documents.student.index');
        Route::post('/documents',       [\App\Http\Controllers\DocumentRequestController::class, 'studentStore'])->name('documents.student.store');
    });
    Route::middleware('role:registrar,admin')->group(function () {
        Route::get(  '/registrar/documents',                         [\App\Http\Controllers\DocumentRequestController::class, 'registrarIndex'])->name('documents.registrar.index');
        Route::patch('/registrar/documents/{documentRequest}/status',[\App\Http\Controllers\DocumentRequestController::class, 'updateStatus'])->name('documents.update-status');
        Route::post( '/registrar/documents/bulk-update',             [\App\Http\Controllers\DocumentRequestController::class, 'bulkUpdate'])->name('documents.bulk-update');
    });

    // ── Faculty Leave Requests ────────────────────────────────────────────
    Route::middleware('role:faculty')->group(function () {
        Route::get( '/faculty/leave',       [\App\Http\Controllers\LeaveRequestController::class, 'facultyIndex'])->name('leave.faculty.index');
        Route::post('/faculty/leave',       [\App\Http\Controllers\LeaveRequestController::class, 'facultyStore'])->name('leave.faculty.store');
    });
    Route::middleware('role:registrar,admin')->group(function () {
        Route::get(  '/admin/leave',                        [\App\Http\Controllers\LeaveRequestController::class, 'adminIndex'])->name('leave.admin.index');
        Route::patch('/admin/leave/{leaveRequest}/review',  [\App\Http\Controllers\LeaveRequestController::class, 'review'])->name('leave.review');
        Route::post( '/admin/leave/bulk-review',            [\App\Http\Controllers\LeaveRequestController::class, 'bulkReview'])->name('leave.bulk-review');
    });

    // ── Assignments ────────────────────────────────────────────────────────
    Route::middleware('role:faculty')->group(function () {
        Route::get( '/faculty/assignments',              [\App\Http\Controllers\AssignmentController::class, 'facultyIndex'])->name('assignments.faculty.index');
        Route::post('/faculty/assignments',              [\App\Http\Controllers\AssignmentController::class, 'facultyStore'])->name('assignments.faculty.store');
        Route::get( '/faculty/assignments/{assignment}', [\App\Http\Controllers\AssignmentController::class, 'facultyShow'])->name('assignments.faculty.show');
        Route::patch('/faculty/assignments/{assignment}/publish', [\App\Http\Controllers\AssignmentController::class, 'publish'])->name('assignments.publish');
        Route::patch('/faculty/assignments/submissions/{submission}/grade', [\App\Http\Controllers\AssignmentController::class, 'gradeSubmission'])->name('assignments.grade');
    });
    Route::middleware('role:student')->group(function () {
        Route::get( '/student/assignments',                     [\App\Http\Controllers\AssignmentController::class, 'studentIndex'])->name('assignments.student.index');
        Route::post('/student/assignments/{assignment}/submit', [\App\Http\Controllers\AssignmentController::class, 'studentSubmit'])->name('assignments.student.submit');
    });

    // ── Analytics Dashboard ────────────────────────────────────────────────
    Route::middleware('role:registrar,admin')->get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');

    // ── DepEd SF Forms ────────────────────────────────────────────────────
    Route::middleware('role:registrar,admin')->prefix('sf-forms')->name('sf.')->group(function () {
        Route::get('/sf1',  [\App\Http\Controllers\SFFormController::class, 'sf1'])->name('sf1');
        Route::get('/sf2',  [\App\Http\Controllers\SFFormController::class, 'sf2'])->name('sf2');
        Route::get('/sf9',  [\App\Http\Controllers\SFFormController::class, 'sf9'])->name('sf9');
        Route::get('/sf10', [\App\Http\Controllers\SFFormController::class, 'sf10'])->name('sf10');
    });
});

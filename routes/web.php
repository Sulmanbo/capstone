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
    Route::post('/login', [LoginController::class, 'login']);

    // ── Password Recovery (3-step OTP flow) ──────────────────────────────
    // Step 1 — Enter email
    Route::get( '/forgot-password',        [PasswordRecoveryController::class, 'showEmailForm'])->name('password.request');
    Route::post('/forgot-password',        [PasswordRecoveryController::class, 'sendOtp'])->name('password.email');

    // Step 2 — Enter OTP
    Route::get( '/forgot-password/verify', [PasswordRecoveryController::class, 'showVerifyForm'])->name('password.verify-otp');
    Route::post('/forgot-password/verify', [PasswordRecoveryController::class, 'verifyOtp'])->name('password.verify-otp.submit');

    // Step 3 — Set new password
    Route::get( '/forgot-password/reset',  [PasswordRecoveryController::class, 'showResetForm'])->name('password.reset-form');
    Route::post('/forgot-password/reset',  [PasswordRecoveryController::class, 'resetPassword'])->name('password.do-reset');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── Mandatory First-Login Password Reset ──────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get( '/password/reset-required', [ForcePasswordResetController::class, 'show'])  ->name('password.force-reset');
    Route::post('/password/reset-required', [ForcePasswordResetController::class, 'update'])->name('password.force-reset.update');
});

// ── Admin Routes ──────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/security-settings', fn() => 'Security Settings — coming soon')->name('security-settings');
    Route::get('/grades', fn() => 'Grades & Records — coming soon')->name('grades.index');

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
        Route::get('/', [StudentController::class, 'index'])->name('index');
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

    // ── Academic Years Management ─────────────────────────────────────────
    Route::prefix('academic-years')->name('academic-years.')->group(function () {
        Route::get('/',                [AcademicYearController::class, 'index'])   ->name('index');
        Route::get('/create',          [AcademicYearController::class, 'create'])  ->name('create');
        Route::post('/',               [AcademicYearController::class, 'store'])   ->name('store');
        Route::get('/{academicYear}/edit', [AcademicYearController::class, 'edit']) ->name('edit');
        Route::put('/{academicYear}',  [AcademicYearController::class, 'update'])  ->name('update');
        Route::delete('/{academicYear}', [AcademicYearController::class, 'destroy'])->name('destroy');
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
    Route::get('/audit',             [AuditLogController::class,  'index']) ->name('audit.index');
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

    // ── Faculty Schedules ─────────────────────────────────────────────────
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/',              [\App\Http\Controllers\Admin\FacultyScheduleController::class, 'index'])  ->name('index');
        Route::post('/',             [\App\Http\Controllers\Admin\FacultyScheduleController::class, 'store'])  ->name('store');
        Route::put('/{schedule}',    [\App\Http\Controllers\Admin\FacultyScheduleController::class, 'update']) ->name('update');
        Route::delete('/{schedule}', [\App\Http\Controllers\Admin\FacultyScheduleController::class, 'destroy'])->name('destroy');
    });

    // ── Admin Settings ────────────────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',          [AdminSettingsController::class, 'index'])          ->name('index');
        Route::post('/security', [AdminSettingsController::class, 'updateSecurity']) ->name('security');
        Route::post('/password', [AdminSettingsController::class, 'updatePassword']) ->name('password');
    });
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
    });

    // Faculty Dashboard & Pages
    Route::middleware('role:faculty')->group(function () {
        Route::get('/faculty/dashboard',      [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'index'])       ->name('faculty.dashboard');
        Route::get('/faculty/my-classes',     [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'myClasses'])   ->name('faculty.classes');
        Route::get('/faculty/gradebook',      [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'gradebook'])   ->name('faculty.gradebook');
        Route::get('/faculty/attendance',     [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'attendance'])  ->name('faculty.attendance');
        Route::get('/faculty/my-schedule',    [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'mySchedule'])  ->name('faculty.my-schedule');
        Route::get('/faculty/announcements',  [App\Http\Controllers\Dashboard\FacultyDashboardController::class, 'announcements'])->name('faculty.announcements');
    });

    // Student Dashboard
    Route::get('/student/dashboard', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'index'])
        ->middleware('role:student')
        ->name('student.dashboard');

    Route::get('/student/report-card', [App\Http\Controllers\Dashboard\StudentDashboardController::class, 'reportCard'])
        ->middleware('role:student')
        ->name('student.report-card');

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
});

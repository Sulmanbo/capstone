<?php

use App\Models\User;

// ── Page ────────────────────────────────────────────────────────────────────

test('login page loads', function () {
    $this->get('/login')->assertOk();
});

test('authenticated user is redirected away from login', function () {
    $user = User::factory()->student()->create();
    $this->actingAs($user)->get('/login')->assertRedirect();
});

// ── Successful login & role redirects ───────────────────────────────────────

test('student logs in and lands on student dashboard', function () {
    $user = User::factory()->student()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password'])
         ->assertRedirect(route('student.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('admin logs in and lands on admin dashboard', function () {
    $user = User::factory()->admin()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password'])
         ->assertRedirect(route('admin.dashboard'));
});

test('faculty logs in and lands on faculty dashboard', function () {
    $user = User::factory()->faculty()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password'])
         ->assertRedirect(route('faculty.dashboard'));
});

test('registrar logs in and lands on registrar dashboard', function () {
    $user = User::factory()->registrar()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password'])
         ->assertRedirect(route('registrar.dashboard'));
});

// ── Failures ─────────────────────────────────────────────────────────────────

test('wrong password is rejected with an error', function () {
    $user = User::factory()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'wrong'])
         ->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('unknown username is rejected', function () {
    $this->post('/login', ['username' => 'nobody_here', 'password' => 'password'])
         ->assertSessionHasErrors('username');

    $this->assertGuest();
});

test('failed_attempts increments on each bad password', function () {
    $user = User::factory()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'bad']);

    expect($user->fresh()->failed_attempts)->toBe(1);
});

// ── Lockout ───────────────────────────────────────────────────────────────────

test('account is locked after 5 failed attempts', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $_) {
        $this->post('/login', ['username' => $user->username, 'password' => 'bad']);
    }

    $user->refresh();
    expect($user->status)->toBe('locked');
    expect($user->failed_attempts)->toBe(5);
});

test('locked account is rejected even with the correct password', function () {
    $user = User::factory()->locked()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password'])
         ->assertSessionHasErrors('username');

    $this->assertGuest();
});

// ── Force-password-reset ──────────────────────────────────────────────────────

test('user with password_reset_required is redirected to force-reset page', function () {
    $user = User::factory()->forcePasswordReset()->create();

    $this->post('/login', ['username' => $user->username, 'password' => 'password'])
         ->assertRedirect(route('password.force-reset'));
});

// ── Logout ────────────────────────────────────────────────────────────────────

test('authenticated user can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));

    $this->assertGuest();
});

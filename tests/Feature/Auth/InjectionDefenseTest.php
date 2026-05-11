<?php

use App\Models\User;

it('allows legitimate names with apostrophes', function () {
    $admin = User::factory()->admin()->create();

    $legitimateInputs = [
        "O'Brien",
        "Anna-Marie",
        "de la Cruz",
        "Mary O'Connor",
        "Andre",
    ];

    foreach ($legitimateInputs as $name) {
        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'first_name' => $name,
                'last_name'  => 'Test',
                'gender'     => 'male',
                'role_id'    => '01',
            ]);

        // Should NOT be 403
        expect($response->status())->not->toBe(403, "Legitimate input rejected: {$name}");
    }
});

it('blocks classic SQL injection patterns', function () {
    config(['security.injection_defense_mode' => 'block']);

    $response = $this->post('/login', [
        'username' => "admin' OR 1=1 --",
        'password' => 'whatever',
    ]);

    expect($response->status())->toBe(403);
});

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'              => fake()->firstName(),
            'last_name'               => fake()->lastName(),
            'email'                   => fake()->unique()->safeEmail(),
            'username'                => fake()->unique()->userName(),
            'password'                => bcrypt('password'),
            'role_id'                 => '01',
            'status'                  => 'active',
            'password_reset_required' => false,
            'failed_attempts'         => 0,
        ];
    }

    public function admin(): static    { return $this->state(['role_id' => '04']); }
    public function faculty(): static  { return $this->state(['role_id' => '02']); }
    public function registrar(): static{ return $this->state(['role_id' => '03']); }
    public function student(): static  { return $this->state(['role_id' => '01']); }

    public function forcePasswordReset(): static
    {
        return $this->state(['password_reset_required' => true]);
    }

    public function locked(): static
    {
        return $this->state([
            'status'          => 'locked',
            'locked_until'    => now()->addMinutes(10),
            'failed_attempts' => 5,
        ]);
    }
}

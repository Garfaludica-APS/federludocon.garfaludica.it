<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
	protected static ?string $password;

	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'username' => fake()->unique()->lastName(),
			'email' => fake()->unique()->safeEmail(),
			'password' => static::$password ??= Hash::make('password'),
			'remember_token' => Str::random(10),
			'is_super_admin' => fake()->boolean(),
			'invitation_id' => Invitation::factory(),
		];
	}
}

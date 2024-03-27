<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
	protected static ?string $token;

	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'email' => fake()->unique()->safeEmail(),
			'token' => static::$token ??= Hash::make(Str::random(60)),
			'is_super_admin' => fake()->boolean(),
			'created_by' => null,
			'accepted_at' => fake()->dateTime(now()),
		];
	}
}

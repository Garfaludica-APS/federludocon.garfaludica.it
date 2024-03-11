<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class DeleteAccountTest extends TestCase
{
	use RefreshDatabase;

	public function testUserAccountsCanBeDeleted(): void
	{
		if (!Features::hasAccountDeletionFeatures())
			static::markTestSkipped('Account deletion is not enabled.');

		$this->actingAs($user = User::factory()->create());

		$response = $this->delete('/user', [
			'password' => 'password',
		]);

		static::assertNull($user->fresh());
	}

	public function testCorrectPasswordMustBeProvidedBeforeAccountCanBeDeleted(): void
	{
		if (!Features::hasAccountDeletionFeatures())
			static::markTestSkipped('Account deletion is not enabled.');

		$this->actingAs($user = User::factory()->create());

		$response = $this->delete('/user', [
			'password' => 'wrong-password',
		]);

		static::assertNotNull($user->fresh());
	}
}

<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class TwoFactorAuthenticationSettingsTest extends TestCase
{
	use RefreshDatabase;

	public function testTwoFactorAuthenticationCanBeEnabled(): void
	{
		if (!Features::canManageTwoFactorAuthentication())
			static::markTestSkipped('Two factor authentication is not enabled.');

		$this->actingAs($user = User::factory()->create());

		$this->withSession(['auth.password_confirmed_at' => time()]);

		$response = $this->post('/user/two-factor-authentication');

		static::assertNotNull($user->fresh()->two_factor_secret);
		static::assertCount(8, $user->fresh()->recoveryCodes());
	}

	public function testRecoveryCodesCanBeRegenerated(): void
	{
		if (!Features::canManageTwoFactorAuthentication())
			static::markTestSkipped('Two factor authentication is not enabled.');

		$this->actingAs($user = User::factory()->create());

		$this->withSession(['auth.password_confirmed_at' => time()]);

		$this->post('/user/two-factor-authentication');
		$this->post('/user/two-factor-recovery-codes');

		$user = $user->fresh();

		$this->post('/user/two-factor-recovery-codes');

		static::assertCount(8, $user->recoveryCodes());
		static::assertCount(8, array_diff($user->recoveryCodes(), $user->fresh()->recoveryCodes()));
	}

	public function testTwoFactorAuthenticationCanBeDisabled(): void
	{
		if (!Features::canManageTwoFactorAuthentication())
			static::markTestSkipped('Two factor authentication is not enabled.');

		$this->actingAs($user = User::factory()->create());

		$this->withSession(['auth.password_confirmed_at' => time()]);

		$this->post('/user/two-factor-authentication');

		static::assertNotNull($user->fresh()->two_factor_secret);

		$this->delete('/user/two-factor-authentication');

		static::assertNull($user->fresh()->two_factor_secret);
	}
}

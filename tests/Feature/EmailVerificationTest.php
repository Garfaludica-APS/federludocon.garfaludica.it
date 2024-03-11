<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class EmailVerificationTest extends TestCase
{
	use RefreshDatabase;

	public function testEmailVerificationScreenCanBeRendered(): void
	{
		if (!Features::enabled(Features::emailVerification()))
			static::markTestSkipped('Email verification not enabled.');

		$user = User::factory()->withPersonalTeam()->unverified()->create();

		$response = $this->actingAs($user)->get('/email/verify');

		$response->assertStatus(200);
	}

	public function testEmailCanBeVerified(): void
	{
		if (!Features::enabled(Features::emailVerification()))
			static::markTestSkipped('Email verification not enabled.');

		Event::fake();

		$user = User::factory()->unverified()->create();

		$verificationUrl = URL::temporarySignedRoute(
			'verification.verify',
			now()->addMinutes(60),
			['id' => $user->id, 'hash' => sha1($user->email)]
		);

		$response = $this->actingAs($user)->get($verificationUrl);

		Event::assertDispatched(Verified::class);

		static::assertTrue($user->fresh()->hasVerifiedEmail());
		$response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
	}

	public function testEmailCanNotVerifiedWithInvalidHash(): void
	{
		if (!Features::enabled(Features::emailVerification()))
			static::markTestSkipped('Email verification not enabled.');

		$user = User::factory()->unverified()->create();

		$verificationUrl = URL::temporarySignedRoute(
			'verification.verify',
			now()->addMinutes(60),
			['id' => $user->id, 'hash' => sha1('wrong-email')]
		);

		$this->actingAs($user)->get($verificationUrl);

		static::assertFalse($user->fresh()->hasVerifiedEmail());
	}
}

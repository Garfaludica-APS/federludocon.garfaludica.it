<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class ProfileInformationTest extends TestCase
{
	use RefreshDatabase;

	public function testProfileInformationCanBeUpdated(): void
	{
		$this->actingAs($user = User::factory()->create());

		$response = $this->put('/user/profile-information', [
			'name' => 'Test Name',
			'email' => 'test@example.com',
		]);

		static::assertSame('Test Name', $user->fresh()->name);
		static::assertSame('test@example.com', $user->fresh()->email);
	}
}

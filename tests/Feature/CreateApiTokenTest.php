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
class CreateApiTokenTest extends TestCase
{
	use RefreshDatabase;

	public function testApiTokensCanBeCreated(): void
	{
		if (!Features::hasApiFeatures())
			static::markTestSkipped('API support is not enabled.');

		$this->actingAs($user = User::factory()->withPersonalTeam()->create());

		$response = $this->post('/user/api-tokens', [
			'name' => 'Test Token',
			'permissions' => [
				'read',
				'update',
			],
		]);

		static::assertCount(1, $user->fresh()->tokens);
		static::assertSame('Test Token', $user->fresh()->tokens->first()->name);
		static::assertTrue($user->fresh()->tokens->first()->can('read'));
		static::assertFalse($user->fresh()->tokens->first()->can('delete'));
	}
}

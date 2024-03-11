<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class ApiTokenPermissionsTest extends TestCase
{
	use RefreshDatabase;

	public function testApiTokenPermissionsCanBeUpdated(): void
	{
		if (!Features::hasApiFeatures())
			static::markTestSkipped('API support is not enabled.');

		$this->actingAs($user = User::factory()->withPersonalTeam()->create());

		$token = $user->tokens()->create([
			'name' => 'Test Token',
			'token' => Str::random(40),
			'abilities' => ['create', 'read'],
		]);

		$response = $this->put('/user/api-tokens/' . $token->id, [
			'name' => $token->name,
			'permissions' => [
				'delete',
				'missing-permission',
			],
		]);

		static::assertTrue($user->fresh()->tokens->first()->can('delete'));
		static::assertFalse($user->fresh()->tokens->first()->can('read'));
		static::assertFalse($user->fresh()->tokens->first()->can('missing-permission'));
	}
}

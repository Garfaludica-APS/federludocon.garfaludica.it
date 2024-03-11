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
class BrowserSessionsTest extends TestCase
{
	use RefreshDatabase;

	public function testOtherBrowserSessionsCanBeLoggedOut(): void
	{
		$this->actingAs($user = User::factory()->create());

		$response = $this->delete('/user/other-browser-sessions', [
			'password' => 'password',
		]);

		$response->assertSessionHasNoErrors();
	}
}

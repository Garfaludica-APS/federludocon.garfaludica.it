<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class ExampleTest extends TestCase
{
	/**
	 * A basic test example.
	 */
	public function testTheApplicationReturnsASuccessfulResponse(): void
	{
		$response = $this->get('/');

		$response->assertStatus(200);
	}
}

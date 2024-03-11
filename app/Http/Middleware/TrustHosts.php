<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
	/**
	 * Get the host patterns that should be trusted.
	 *
	 * @return array<int, string|null>
	 */
	public function hosts(): array
	{
		return [
			$this->allSubdomainsOfApplicationUrl(),
		];
	}
}

<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\RateLimiters;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StartBookingRateLimiter
{
	protected RateLimiter $limiter;

	public function __construct(RateLimiter $limiter)
	{
		$this->limiter = $limiter;
	}

	public function attempts(Request $request): mixed
	{
		return $this->limiter->attempts($this->throttleKey($request));
	}

	public function tooManyAttempts(Request $request): bool
	{
		return $this->limiter->tooManyAttempts($this->throttleKey($request), 1);
	}

	public function increment(Request $request): void
	{
		$this->limiter->hit($this->throttleKey($request), 180);
	}

	public function availableIn(Request $request): int
	{
		return $this->limiter->availableIn($this->throttleKey($request));
	}

	public function clear(Request $request): void
	{
		$this->limiter->clear($this->throttleKey($request));
	}

	protected function throttleKey(Request $request): string
	{
		return Str::transliterate(Str::lower($request->input('email')));
	}
}

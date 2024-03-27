<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\RateLimiters;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoginRateLimiter
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
		return $this->limiter->tooManyAttempts($this->throttleKey($request), 5);
	}

	public function increment(Request $request): void
	{
		$this->limiter->hit($this->throttleKey($request), 60);
	}

	public function availableIn(Request $request): int
	{
		return $this->limiter->availableIn($this->throttleKey($request));
	}

	public function clear(Request $request): void
	{
		$this->limiter->clear($this->throttleKey($request));
	}

	protected function throttleKey(Request $request): void
	{
		Str::transliterate(Str::lower($request->input('username')));
	}
}

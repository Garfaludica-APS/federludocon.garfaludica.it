<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\RateLimiters;

use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;

class LoginRateLimiter
{
	protected RateLimiter $limiter;

	public function __construct(RateLimiter $limiter)
	{
		$this->limiter = $limiter;
	}

	public function attempts(LoginRequest $request): mixed
	{
		return $this->limiter->attempts($this->throttleKey($request));
	}

	public function tooManyAttempts(LoginRequest $request): bool
	{
		return $this->limiter->tooManyAttempts($this->throttleKey($request), 5);
	}

	public function increment(LoginRequest $request): void
	{
		$this->limiter->hit($this->throttleKey($request), 60);
	}

	public function availableIn(LoginRequest $request): int
	{
		return $this->limiter->availableIn($this->throttleKey($request));
	}

	public function clear(LoginRequest $request): void
	{
		$this->limiter->clear($this->throttleKey($request));
	}

	protected function throttleKey(LoginRequest $request): string
	{
		return Str::transliterate(Str::lower($request->input('username')));
	}
}

<?php

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

	public function attempts(Request $request)
	{
		return $this->limiter->attempts($this->throttleKey($request));
	}

	public function tooManyAttempts(Request $request)
	{
		return $this->limiter->tooManyAttempts($this->throttleKey($request), 5);
	}

	public function increment(Request $request)
	{
		$this->limiter->hit($this->throttleKey($request), 60);
	}

	public function availableIn(Request $request)
	{
		return $this->limiter->availableIn($this->throttleKey($request));
	}

	public function clear(Request $request)
	{
		$this->limiter->clear($this->throttleKey($request));
	}

	protected function throttleKey(Request $request)
	{
		Str::transliterate(Str::lower($request->input('username')) . '|' . $request->ip());
	}
}

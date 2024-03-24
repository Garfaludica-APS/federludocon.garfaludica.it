<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void {}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Password::defaults(function () {
			return $this->app->isProduction()
				? Password::min(8)
					->letters()
					->mixedCase()
					->numbers()
					->symbols()
					->uncompromised()
				: Password::min(8);
		});
		RateLimiter::for('login', function (Request $request) {
			$throttleKey = Str::transliterate(Str::lower($request->input('username')) . '|' . $request->ip());
			return Limit::perMinute(5)->by($throttleKey)->response(
				function (Request $request, array $headers) use ($throttleKey)
				{
					$seconds = RateLimiter::availableIn($throttleKey);
					return redirect()->back()->withErrors([
						'login' => __('Too many login attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])
					])->onlyInput('username', 'email', 'remember');
				}
			);
		});
	}
}

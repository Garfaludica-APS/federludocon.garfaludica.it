<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
		Password::defaults(function() {
			return $this->app->isProduction()
				? Password::min(8)
					->letters()
					->mixedCase()
					->numbers()
					->symbols()
					->uncompromised()
				: Password::min(8);
		});

		RateLimiter::for('login', function(Request $request) {
			return $this->app->isProduction()
				? Limit::perMinutes(5, 15)->by(request()->ip())
				: Limit::none();
		});
		RateLimiter::for('password-reset', function(Request $request) {
			return $this->app->isProduction()
				? Limit::perMinutes(15, 3)->by(request()->ip())
				: Limit::none();
		});
	}
}

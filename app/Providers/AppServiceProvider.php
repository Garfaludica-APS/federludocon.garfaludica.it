<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Providers;

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
	}
}

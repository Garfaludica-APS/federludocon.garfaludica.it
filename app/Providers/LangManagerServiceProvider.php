<?php

namespace App\Providers;

use App\Lang\Manager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class LangManagerServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->app->singleton(Manager::class, function (Application $app) {
			return new Manager($app->makeWith(Manager::class, ['fileSystem' => Storage::disk('translations')]));
		});
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		//
	}

	public function provides(): array
	{
		return [Manager::class];
	}
}

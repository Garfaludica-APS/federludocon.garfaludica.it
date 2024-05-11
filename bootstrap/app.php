<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LocalizeApp;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

return Application::configure(basePath: \dirname(__DIR__))
	->withRouting(
		web: __DIR__ . '/../routes/web.php',
		commands: __DIR__ . '/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(static function(Middleware $middleware): void {
		$middleware->replace(
			\Illuminate\Http\Middleware\TrustProxies::class,
			\Monicahq\Cloudflare\Http\Middleware\TrustProxies::class
		);
		$middleware->encryptCookies(except: [
			'lang',
		]);
		$middleware->alias([
			'lang' => LocalizeApp::class,
		]);
		$middleware->web(append: [
			HandleInertiaRequests::class,
		]);
		$middleware->redirectGuestsTo(
			static fn(Request $request) => App::isLocale('en') ? route('en.auth.login') : route('auth.login')
		);
		$middleware->redirectUsersTo(
			static fn(Request $request) => route('admin.dashboard')
		);
	})
	->withSchedule(static function(Schedule $schedule): void {
		$schedule->command('auth:clear-resets')->everyFifteenMinutes();
		$schedule->command('cloudflare:reload')->daily()->environments('production');
		$schedule->command('bookings:expire')->everyThreeMinutes();
	})
	->withExceptions(static function(Exceptions $exceptions): void {
		$exceptions->respond(static function(RedirectResponse|Response $response, \Throwable $exception, Request $request): RedirectResponse|Response {
			if ($response->status() === 419)
				return back()->with('flash', [
					'message' => __('The page expired, please try again.'),
					'location' => 'modal',
					'timeout' => 7000,
					'style' => 'error',
				]);
			return $response;
		});
	})->create();

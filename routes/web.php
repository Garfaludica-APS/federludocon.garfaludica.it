<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InviteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group([
	'middleware' => 'lang:pub',
], static function(): void {
	Route::get('/', static function() {
		return Inertia::render('Home', [
			'tdgLogo' => asset('storage/images/tdg-logo.png'),
		]);
	})->name('home');
	Route::get('/about', static fn() => Inertia::render('About'))->name('about');
	Route::get('/tables', static fn() => Inertia::render('About'))->name('tables');
	Route::get('/hotels', static fn() => Inertia::render('Hotels'))->name('hotels');
	Route::get('/venue', static fn() => Inertia::render('Venue'))->name('venue');
	Route::get('/organization', static fn() => Inertia::render('Organization'))->name('organization');
	Route::get('/contact', static fn() => Inertia::render('Contact'))->name('contact');
	Route::get('/book', static fn() => Inertia::render('Book'))->name('book');
	Route::get('/license', static fn() => Inertia::render('License'))->name('license');
});

Route::group([
	'middleware' => 'lang:pub',
	'prefix' => 'en',
	'as' => 'en.',
], static function(): void {
	Route::get('/', static function() {
		return Inertia::render('Home', [
			'tdgLogo' => asset('storage/images/tdg-logo.png'),
		]);
	})->name('home');
	Route::get('/about', static fn() => Inertia::render('About'))->name('about');
	Route::get('/tables', static fn() => Inertia::render('About'))->name('tables');
	Route::get('/hotels', static fn() => Inertia::render('Hotels'))->name('hotels');
	Route::get('/venue', static fn() => Inertia::render('Venue'))->name('venue');
	Route::get('/organization', static fn() => Inertia::render('Organization'))->name('organization');
	Route::get('/contact', static fn() => Inertia::render('Contact'))->name('contact');
	Route::get('/book', static fn() => Inertia::render('Book'))->name('book');
});

Route::group([
	'middleware' => ['lang:admin', 'auth:web'],
	'prefix' => 'admin',
	'as' => 'admin.',
], static function(): void {
	Route::get('/', static fn() => to_route('admin.dashboard'))->name('index');
	Route::get('/dashboard', DashboardController::class)->name('dashboard');
	Route::resource('admins', AdminController::class)->except([
		'show', 'create', 'store',
	]);
	Route::resource('invites', InviteController::class)->only([
		'store', 'destroy',
	]);
})->name('admin');

Route::get('/en/admin', static fn() => to_route('admin.dashboard'))->middleware(['lang:pub', 'auth:web'])->name('en.admin.index');

Route::group([
	'middleware' => ['lang:admin', 'guest:web'],
	'prefix' => 'admin',
	'controller' => AuthController::class,
	'as' => 'auth.',
], static function(): void {
	Route::get('/login', 'login')->withoutMiddleware('lang:admin')->middleware('lang:pub')->name('login');
	Route::post('/login', 'authenticate')->name('authenticate');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('auth.logout');

Route::get('/accept-invite/{invite}/{token}', [AdminController::class, 'create'])->middleware(['lang:admin', 'guest:web'])->name('auth.admin.create');

Route::post('/accept-invite/{invite}', [AdminController::class, 'store'])->middleware(['lang:admin', 'guest:web'])->name('auth.admin.store');

Route::get('/en/admin/login', [AuthController::class, 'login'])->middleware(['lang:pub', 'guest:web'])->name('en.auth.login');

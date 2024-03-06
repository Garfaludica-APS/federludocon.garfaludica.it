<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return Inertia::render('Home', [
		'tdgLogo' => asset('storage/images/tdg-logo.png'),
	]);
})->name('home');

Route::get('/en', function () {
	App::setLocale('en');
	return Inertia::render('Home', [
		'tdgLogo' => asset('storage/images/tdg-logo.png'),
	]);
})->name('en.home');

Route::get('/about', function () {
	return Inertia::render('About');
})->name('about');

Route::get('/en/about', function () {
	App::setLocale('en');
	return Inertia::render('About');
})->name('en.about');

Route::get('/tables', function () {
	return Inertia::render('About');
})->name('tables');

Route::get('/en/tables', function () {
	App::setLocale('en');
	return Inertia::render('Tables');
})->name('en.tables');

Route::get('/hotels', function () {
	return Inertia::render('Hotels');
})->name('hotels');

Route::get('/en/hotels', function () {
	App::setLocale('en');
	return Inertia::render('Hotels');
})->name('en.hotels');

Route::get('/venue', function () {
	return Inertia::render('Venue');
})->name('venue');

Route::get('/en/venue', function () {
	App::setLocale('en');
	return Inertia::render('Venue');
})->name('en.venue');

Route::get('/organization', function () {
	return Inertia::render('Organization');
})->name('organization');

Route::get('/en/organization', function () {
	App::setLocale('en');
	return Inertia::render('Organization');
})->name('en.organization');

Route::get('/contact', function () {
	return Inertia::render('Contact');
})->name('contact');

Route::get('/en/contact', function () {
	App::setLocale('en');
	return Inertia::render('Contact');
})->name('en.contact');

Route::get('/book', function () {
	return Inertia::render('Book');
})->name('book');

Route::get('/en/book', function () {
	App::setLocale('en');
	return Inertia::render('Book');
})->name('en.book');

Route::get('/license', function () {
	return Inertia::render('License');
})->name('license');

// Route::middleware([
// 	'auth:sanctum',
// 	config('jetstream.auth_session'),
// 	'verified',
// ])->group(function () {
// 	Route::get('/dashboard', function () {
// 		return Inertia::render('Dashboard');
// 	})->name('dashboard');
// });

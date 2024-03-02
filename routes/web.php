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
		'loripsum' => file_get_contents('https://loripsum.net/api/10/long/decorate/link/ul/ol/dl/bq/code/headers')
	]);
})->name('home');

Route::get('/about', function () {
	return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
	return Inertia::render('Contact');
})->name('contact');

Route::get('/event', function () {
	return Inertia::render('Event');
})->name('event');

Route::get('/reach-us', function () {
	return Inertia::render('ReachUs');
})->name('reach-us');

Route::get('/book', function () {
	return Inertia::render('Book');
})->name('book');

// Route::middleware([
// 	'auth:sanctum',
// 	config('jetstream.auth_session'),
// 	'verified',
// ])->group(function () {
// 	Route::get('/dashboard', function () {
// 		return Inertia::render('Dashboard');
// 	})->name('dashboard');
// });

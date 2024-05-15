<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Hotel\ExternalBookingController;
use App\Http\Controllers\Admin\Hotel\MealController;
use App\Http\Controllers\Admin\Hotel\PresentationController;
use App\Http\Controllers\Admin\Hotel\RoomController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ModalController;
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
	Route::get('/hotels', HotelController::class)->name('hotels');
	Route::get('/venue', static fn() => Inertia::render('Venue'))->name('venue');
	Route::get('/organization', static fn() => Inertia::render('Organization'))->name('organization');
	Route::get('/contact', static fn() => Inertia::render('Contact'))->name('contact');
	Route::get('/book', [BookController::class, 'show'])->name('book');
	Route::get('/privacy', [ModalController::class, 'privacy'])->name('modal.privacy');
	Route::get('/terms', [ModalController::class, 'terms'])->name('modal.terms');
	Route::get('/refund', [ModalController::class, 'refund'])->name('modal.refund');
	Route::get('/license', [ModalController::class, 'license'])->name('modal.license');
});

Route::group([
	'middleware' => 'lang:booking',
], static function(): void {
	Route::post('/book', [BookController::class, 'submit'])->middleware('throttle:start-booking')->name('book.start');

	Route::get('/booking/{booking}', [BookingController::class, 'start'])->name('booking.start');
	Route::get('/booking/{booking}/rooms', [BookingController::class, 'rooms'])->name('booking.rooms');
	Route::put('/booking/{booking}/rooms', [BookingController::class, 'addRoom'])->name('booking.rooms.store');
	Route::delete('/booking/{booking}/rooms/{reservation}', [BookingController::class, 'deleteRoom'])->name('booking.rooms.delete');
	Route::get('/booking/{booking}/meals', [BookingController::class, 'meals'])->name('booking.meals');
	Route::patch('/booking/{booking}/meals', [BookingController::class, 'editMeals'])->name('booking.meals.edit');
	Route::post('/booking/{booking}/meals', [BookingController::class, 'storeNotes'])->name('booking.notes.store');
	Route::get('/booking/{booking}/billing', [BookingController::class, 'billing'])->name('booking.billing');
	Route::post('/booking/{booking}/billing', [BookingController::class, 'storeBilling'])->name('booking.billing.store');
	Route::get('/booking/{booking}/summary', [BookingController::class, 'summary'])->name('booking.summary');
	Route::post('/booking/{booking}/room/{room}/available-checkouts', [BookingController::class, 'availableCheckouts'])->name('booking.room.available-checkouts');
	Route::post('/booking/{booking}/room/{room}/max-people', [BookingController::class, 'maxPeople'])->name('booking.room.max-people');
	Route::post('/boooking/{booking}/terminate', [BookingController::class, 'terminate'])->name('booking.terminate');
	Route::delete('/booking/{booking}/reset', [BookingController::class, 'resetOrder'])->name('booking.reset');

	Route::post('/booking/{booking}/order/createOrder', [BookingController::class, 'createOrder'])->name('booking.createOrder');
	Route::post('/booking/{booking}/order/{orderId}/capture', [BookingController::class, 'captureOrder'])->name('booking.captureOrder');
	Route::get('/booking/{booking}/success', [BookingController::class, 'successOrder'])->name('booking.success');
	Route::get('/booking/{booking}/abort', [BookingController::class, 'abortOrder'])->name('booking.abort');

	Route::get('/booking/{booking}/manage', [BookingController::class, 'manageBooking'])->name('booking.manage');
	Route::post('/booking/{booking}/refund', [BookingController::class, 'refundBooking'])->name('booking.refund');

	Route::patch('/booking/{booking}/billing', [BookingController::class, 'updateBilling'])->name('booking.update-billing');
	Route::patch('/booking/{booking}/add-notes', [BookingController::class, 'addNotes'])->name('booking.add-notes');

	Route::post('/booking/{booking}/add-discount', [BookingController::class, 'addDiscount'])->name('booking.discount.add');
	Route::post('/booking/{booking}/confirm', [BookingController::class, 'confirmBooking'])->name('booking.confirm');
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
	Route::get('/hotels', HotelController::class)->name('hotels');
	Route::get('/venue', static fn() => Inertia::render('Venue'))->name('venue');
	Route::get('/organization', static fn() => Inertia::render('Organization'))->name('organization');
	Route::get('/contact', static fn() => Inertia::render('Contact'))->name('contact');
	Route::get('/book', [BookController::class, 'show'])->name('book');
	Route::get('/privacy', [ModalController::class, 'privacy'])->name('modal.privacy');
	Route::get('/terms', [ModalController::class, 'terms'])->name('modal.terms');
	Route::get('/refund', [ModalController::class, 'refund'])->name('modal.refund');
});

Route::group([
	'middleware' => ['lang:admin', 'auth:web'],
	'prefix' => 'admin',
	'as' => 'admin.',
], static function(): void {
	Route::get('/', static fn() => to_route('admin.dashboard'))->name('index');
	Route::get('/dashboard', DashboardController::class)->name('dashboard');
	Route::resource('admins', AdminController::class)->only([
		'index', 'destroy',
	]);
	Route::resource('invitations', InvitationController::class)->only([
		'store', 'destroy',
	]);
	Route::singleton('hotel.presentation', PresentationController::class)->only([
		'edit', 'update',
	]);
	Route::resource('hotel.rooms', RoomController::class)->except([
		'show',
	]);
	Route::patch('/hotel/{hotel}/rooms/{room}/restore', [RoomController::class, 'restore'])->withTrashed()->name('hotel.rooms.restore');
	Route::resource('hotel.meals', MealController::class)->except([
		'show',
	]);
	Route::patch('/hotel/{hotel}/meals/{meal}/restore', [MealController::class, 'restore'])->withTrashed()->name('hotel.meals.restore');
	Route::resource('hotel.rooms.externalBookings', ExternalBookingController::class)->except([
		'index', 'show', 'create',
	]);
	Route::resource('hotel.externalBookings', ExternalBookingController::class)->only([
		'index', 'create',
	]);
	Route::resource('bookings', AdminBookingController::class)->only([
		'index', 'show',
	]);

	Route::post('/bookings/{booking}/mark-refunded', [AdminBookingController::class, 'markRefunded'])->name('bookings.mark-refunded');
	Route::get('/bookings/{booking}/invoice', [AdminBookingController::class, 'invoice'])->name('bookings.invoice');
})->name('admin');

Route::get('/en/admin', static fn() => to_route('admin.dashboard'))->middleware(['lang:pub', 'auth:web'])->name('en.admin.index');

Route::get('/accept-invite/{invitation}/{token}', [InvitationController::class, 'acceptForm'])->middleware(['lang:admin', 'guest:web'])->name('admin.invitations.accept');
Route::post('/accept-invite/{invitation}', [InvitationController::class, 'accept'])->middleware(['lang:admin', 'guest:web'])->name('admin.register');

Route::group([
	'middleware' => ['lang:pub', 'guest:web'],
	'prefix' => 'admin',
	'controller' => AuthController::class,
	'as' => 'auth.',
], static function(): void {
	Route::get('/login', 'login')->withoutMiddleware('lang:admin')->middleware('lang:pub')->name('login');
	Route::get('/forgot-password', 'forgotPassword')->name('password.forgot');
});

Route::group([
	'middleware' => ['lang:admin', 'guest:web'],
	'prefix' => 'admin',
	'controller' => AuthController::class,
	'as' => 'auth.',
], static function(): void {
	Route::post('/login', 'authenticate')->middleware('throttle:login')->name('authenticate');
	Route::post('/forgot-password', 'sendPasswordResetLink')->middleware('throttle:password-reset')->name('password.email');
	Route::post('/reset-password', 'updatePassword')->name('password.update');
});

Route::get('/en/admin/login', [AuthController::class, 'login'])->middleware(['lang:pub', 'guest:web'])->name('en.auth.login');

Route::get('/en/forgot-password', [AuthController::class, 'forgotPassword'])->middleware(['lang:pub', 'guest:web'])->name('en.auth.password.forgot');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->middleware(['lang:admin', 'guest:web'])->name('password.reset');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web')->name('auth.logout');

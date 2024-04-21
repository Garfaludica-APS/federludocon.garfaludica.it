<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class BookingController extends Controller
{
	public function start(Request $request, Booking $booking): RedirectResponse
	{
		if (!$request->hasValidSignature())
			abort(404);

		if (\in_array($booking->state, [BookingState::PAYMENT, BookingState::COMPLETED, BookingState::FAILED, BookingState::CANCELLED, BookingState::REFUNDED]))
			abort(404);

		$request->session()->invalidate();

		$request->session()->put([
			'booking' => $booking->id,
			'expires' => now()->addMinutes(config('gobcon.session_lifetime', 15)),
		]);

		if ($booking->state === BookingState::SUMMARY)
			return redirect()->route('booking.summary');
		if ($booking->state === BookingState::BILLING)
			return redirect()->route('booking.billing');
		if ($booking->state === BookingState::MEALS)
			return redirect()->route('booking.meals');
		$booking->state = BookingState::ROOMS;
		$booking->save();
		return redirect()->route('booking.rooms', [
			'booking' => $booking,
		]);
	}

	public function rooms(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, [BookingState::ROOMS, BookingState::MEALS]);

		$prevState = $booking->state;
		$booking->state = BookingState::ROOMS;

		$booking->save();
		return inertia('Booking/Rooms', [
			'booking' => $booking,
		]);
	}

	public function meals(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, [BookingState::ROOMS, BookingState::MEALS, BookingState::BILLING]);

		$prevState = $booking->state;
		$booking->state = BookingState::MEALS;

		$booking->save();
		return inertia('Booking/Meals', [
			'booking' => $booking,
		]);
	}

	public function storeNotes(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::MEALS);

		// TODO: validate

		$booking->state = BookingState::BILLING;

		$booking->save();
		return redirect()->route('booking.billing', [
			'booking' => $booking,
		]);
	}

	public function billing(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, BookingState::BILLING);

		return inertia('Booking/Billing', [
			'booking' => $booking,
		]);
	}

	public function storeBilling(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::BILLING);

		$booking->state = BookingState::SUMMARY;

		$booking->save();
		return redirect()->route('booking.summary', [
			'booking' => $booking,
		]);
	}

	public function summary(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, BookingState::SUMMARY);

		return inertia('Booking/Summary', [
			'booking' => $booking,
		]);
	}

	public function createOrder(Request $request, Booking $booking): JsonResponse {}

	public function captureOrder(Request $request, Booking $booking): JsonResponse {}

	public function addRoom(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::ROOMS);
		// TODO: validate

		return redirect()->back();
	}

	public function editRoom(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::ROOMS);
		// TODO: validate

		return redirect()->back();
	}

	public function deleteRoom(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::ROOMS);
		// TODO: validate

		return redirect()->back();
	}

	public function addMeal(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::MEALS);
		// TODO: validate

		return redirect()->back();
	}

	public function deleteMeal(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::MEALS);
		// TODO: validate

		return redirect()->back();
	}

	protected function assertBookingState(Request $request, Booking $booking, array|BookingState $state): void
	{
		if (!$request->session()->has('booking')
			|| $request->session()->get('booking') !== $booking->id
			|| !$request->session()->has('expires'))
				abort(404);
		$expires = $request->session()->get('expires');
		if ($expires->isPast()) {
			$request->session()->invalidate();
			abort(410);
		}
		if (!\is_array($state))
			$state = [$state];
		$curState = $booking->state;
		if (!\in_array($booking->state, $state))
			abort(403);

		$request->session()->put('expires', now()->addMinutes(config('gobcon.session_lifetime', 15)));
	}
}

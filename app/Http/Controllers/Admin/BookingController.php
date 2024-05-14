<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin;

use App\Enums\BookingState;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Inertia\Response;

class BookingController extends Controller
{
	public function index(): Response
	{
		$bookings = Booking::with('billingInfo')->whereIn('state', [BookingState::COMPLETED, BookingState::CANCELLED, BookingState::REFUND_REQUESTED, BookingState::REFUNDED, BookingState::FAILED])->get();
		foreach ($bookings as $booking)
			$booking->append(['total', 'first_check_in', 'last_check_out', 'hotels']);

		return inertia('Admin/Bookings', [
			'bookings' => $bookings,
		]);
	}

	public function show(Booking $booking): Response
	{
		return inertia('Admin/ShowBooking', [
			'booking' => $booking,
		]);
	}
}

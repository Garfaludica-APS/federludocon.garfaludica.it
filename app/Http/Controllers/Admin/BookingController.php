<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Inertia\Response;

class BookingController extends Controller
{
	public function index(): Response
	{
		return inertia('Admin/Bookings', [
		]);
	}

	public function show(Booking $booking): Response
	{
		return inertia('Admin/ShowBooking', [
			'booking' => $booking,
		]);
	}
}

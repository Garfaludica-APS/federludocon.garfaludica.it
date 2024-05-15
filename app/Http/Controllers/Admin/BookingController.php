<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin;

use App\Enums\BookingState;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Srmklive\PayPal\Services\PayPal;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
		$booking->load('billingInfo', 'rooms', 'rooms.room', 'meals', 'meals.meal');
		$booking->append(['total', 'first_check_in', 'last_check_out', 'hotels']);
		$ppOrder = null;
		if ($booking->pp_order_id)
			$ppOrder = Cache::get('pp_order_' . $booking->pp_order_id);
		if (!$ppOrder && $booking->pp_order_id) {
			$provider = new PayPal();
			$provider->setApiCredentials(config('paypal'));
			$provider->getAccessToken();
			$ppOrder = $provider->showOrderDetails($booking->pp_order_id);
			Cache::rememberForever('pp_order_' . $booking->pp_order_id, function () use ($ppOrder) {
				return $ppOrder;
			});
		}
		return inertia('Admin/ShowBooking', [
			'booking' => $booking,
			'hotels' => Hotel::all(),
			'ppOrder' => $ppOrder,
		]);
	}

	public function markRefunded(Booking $booking): RedirectResponse
	{
		if ($booking->state != BookingState::REFUND_REQUESTED)
			return redirect()->back()->with('flash', [
				'message' => __('This booking cannot be marked as refunded.'),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'error',
			]);
		$booking->state = BookingState::REFUNDED;
		$booking->save();
		return redirect()->back()->with('flash', [
			'message' => __('Booking successfully marked as refunded.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	public function invoice(Booking $booking): StreamedResponse
	{
		$receiptNum = mb_str_pad((string)($booking->short_id), 4, '0', \STR_PAD_LEFT);
		return Storage::download('receipts/' . $booking->id . '.pdf', 'receipt-GARFALUDICA-' . $receiptNum . '.pdf');
	}
}

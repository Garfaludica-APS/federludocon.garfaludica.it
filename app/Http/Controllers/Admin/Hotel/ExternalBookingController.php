<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin\Hotel;

use App\Http\Controllers\Controller;
use App\Models\ExternalBooking;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ExternalBookingController extends Controller
{
	use AuthorizesRequests;

	/**
	 * Display a listing of the resource.
	 */
	public function index(Hotel $hotel): Response
	{
		$this->authorize('viewAny', [ExternalBooking::class, $hotel]);
		$admin = auth()->user();
		$externalBookings = ExternalBooking::with(['room' => function (Builder $query) use ($hotel) {
				$query->where('hotel_id', $hotel->id);
		}])->orderByDesc('created_at')->paginate(20)->through(static fn($item) => [
			'id' => $item->id,
			'room' => $item->room,
			'checkin' => $item->checkin,
			'checkout' => $item->checkout,
			'created_at' => $item->created_at,
			'updated_at' => $item->updated_at,
			'can' => [
				'update' => $admin->can('update', [ExternalBooking::class, $hotel, $item->room, $item]),
				'delete' => $admin->can('delete', [ExternalBooking::class, $hotel, $item->room, $item]),
			],
		]);
		return inertia('Admin/Hotel/ExternalBooking/Index', [
			'admin' => $admin,
			'hotel' => $hotel,
			'externalBookings' => $externalBookings,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Hotel $hotel): Response
	{
		$this->authorize('create', [ExternalBooking::class, $hotel]);
		$hotel->load('rooms');
		return inertia('Admin/Hotel/ExternalBooking/Create', [
			'hotel' => $hotel,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, Hotel $hotel, Room $room): RedirectResponse
	{
		$this->authorize('create', [ExternalBooking::class, $hotel, $room]);

		$validated = $request->validate([
			'checkin' => 'required|date_format:Y-m-d H:i',
			'checkout' => 'required|date_format:Y-m-d H:i|gt:checkin',
		]);

		$room->externalBookings()->create($validated);

		return redirect()->route('admin.hotel.externalBookings.index', $hotel)->with('flash', [
			'message' => __('Booking successfully registered.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Hotel $hotel, Room $room, ExternalBooking $externalBooking): Response
	{
		$this->authorize('update', [ExternalBooking::class, $hotel, $room, $externalBooking]);
		$admin = auth()->user();
		return inertia('Admin/Hotel/ExternalBooking/Edit', [
			'externalBooking' => [
				'id' => $externalBooking->id,
				'hotel' => $hotel,
				'room' => $room,
				'checkin' => $externalBooking->checkin,
				'checkout' => $externalBooking->checkout,
				'can' => [
					'delete' => $admin->can('delete', [ExternalBooking::class, $hotel, $room, $externalBooking]),
				],
			],
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Hotel $hotel, Room $room, ExternalBooking $externalBooking): RedirectResponse
	{
		$this->authorize('update', [ExternalBooking::class, $hotel, $room, $externalBooking]);

		$validated = $request->validate([
			'checkin' => 'required|date_format:Y-m-d H:i',
			'checkout' => 'required|date_format:Y-m-d H:i|gt:checkin',
		]);

		$externalBooking->update($validated);

		return redirect()->route('admin.hotel.externalBookings.index', $hotel)->with('flash', [
			'message' => __('Booking successfully updated.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Hotel $hotel, Room $room, ExternalBooking $externalBooking): RedirectResponse
	{
		$this->authorize('delete', [ExternalBooking::class, $hotel, $room, $externalBooking]);

		$externalBooking->delete();

		return redirect()->route('admin.hotel.externalBookings.index', $hotel)->with('flash', [
			'message' => __('Booking successfully deleted.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}
}

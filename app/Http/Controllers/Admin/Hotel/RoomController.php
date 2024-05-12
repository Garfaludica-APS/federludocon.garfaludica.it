<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

class RoomController extends Controller
{
	use AuthorizesRequests;

	/**
	 * Display a listing of the resource.
	 */
	public function index(Hotel $hotel): Response
	{
		$this->authorize('viewAny', [Room::class, $hotel]);
		$admin = auth()->user();
		return inertia('Admin/Hotel/Rooms/Index', [
			'hotel' => $hotel,
			'rooms' => $hotel->rooms()->withTrashed()->get()->transform(static fn($room) => [
				'id' => $room->id,
				'name' => $room->name,
				'description' => $room->description,
				'quantity' => $room->quantity,
				'buy_options' => $room->buy_options,
				'checkin_time' => $room->checkin_time->format('H:i'),
				'checkout_time' => $room->checkout_time->format('H:i'),
				'created_at' => $room->created_at,
				'updated_at' => $room->updated_at,
				'deleted_at' => $room->deleted_at,
				'can' => [
					'update' => Gate::allows('update', [Room::class, $hotel, $room]),
					'delete' => Gate::allows('delete', [Room::class, $hotel, $room]),
					'restore' => Gate::allows('restore', [Room::class, $hotel, $room]),
				],
			]),
			'canCreateRooms' => $admin->can('create', [Room::class, $hotel]),
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Hotel $hotel): Response
	{
		$this->authorize('create', [Room::class, $hotel]);
		return inertia('Admin/Hotel/Rooms/Create', [
			'hotel' => $hotel,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, Hotel $hotel): RedirectResponse
	{
		$this->authorize('create', [Room::class, $hotel]);

		$validated = $request->validate([
			'name' => 'required|array:it,en',
			'name.it' => 'required|string|max:100',
			'name.en' => 'required|string|max:100',
			'description' => 'nullable|array:it,en',
			'description.it' => 'nullable|string|max:250',
			'description.en' => 'nullable|string|max:250',
			'quantity' => 'required|integer|min:1',
			'buy_options' => 'required|array|min:1',
			'buy_options.*.id' => 'missing',
			'buy_options.*.it' => 'required|string|max:50',
			'buy_options.*.en' => 'required|string|max:50',
			'buy_options.*.people' => 'required|integer|min:0',
			'buy_options.*.price' => 'required|decimal:0,2|min:0',
			'buy_options.*.included_meals' => 'nullable|array',
			'buy_options.*.included_meals.*' => 'string|in:breakfast,lunch,dinner',
			'checkin_time' => 'required|date_format:H:i',
			'checkout_time' => 'required|date_format:H:i|lte:checkin_time',
		]);

		$hotel->rooms()->create($validated);

		return redirect()->route('admin.hotel.rooms.index', $hotel)->with('flash', [
			'message' => __('Room successfully created.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Hotel $hotel, Room $room): Response
	{
		$this->authorize('update', [Room::class, $hotel, $room]);
		$admin = auth()->user();
		return inertia('Admin/Hotel/Rooms/Edit', [
			'room' => [
				'id' => $room->id,
				'hotel' => $hotel,
				'name' => $room->name,
				'description' => $room->description,
				'quantity' => $room->quantity,
				'buy_options' => $room->buy_options,
				'checkin_time' => $room->checkin_time->format('H:i'),
				'checkout_time' => $room->checkout_time->format('H:i'),
				'created_at' => $room->created_at,
				'updated_at' => $room->updated_at,
				'deleted_at' => $room->deleted_at,
				'can' => [
					'delete' => $admin->can('delete', [Room::class, $hotel, $room]),
					'restore' => $admin->can('restore', [Room::class, $hotel, $room]),
				],
			],
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Hotel $hotel, Room $room): RedirectResponse
	{
		$this->authorize('update', [Room::class, $hotel, $room]);

		$validated = $request->validate([
			'name' => 'required|array:it,en',
			'name.it' => 'required|string|max:100',
			'name.en' => 'required|string|max:100',
			'description' => 'nullable|array:it,en',
			'description.it' => 'nullable|string|max:250',
			'description.en' => 'nullable|string|max:250',
			'quantity' => 'required|integer|min:1',
			'buy_options' => 'required|array|min:1',
			'buy_options.*.id' => 'missing',
			'buy_options.*.it' => 'required|string|max:50',
			'buy_options.*.en' => 'required|string|max:50',
			'buy_options.*.people' => 'required|integer|min:0',
			'buy_options.*.price' => 'required|decimal:0,2|min:0',
			'buy_options.*.included_meals' => 'nullable|array',
			'buy_options.*.included_meals.*' => 'string|in:breakfast,lunch,dinner',
			'checkin_time' => 'required|date_format:H:i',
			'checkout_time' => 'required|date_format:H:i|lte:checkin_time',
		]);

		$room->update($validated);

		return redirect()->route('admin.hotel.rooms.index', $hotel)->with('flash', [
			'message' => __('Room successfully updated.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Hotel $hotel, Room $room): RedirectResponse
	{
		$this->authorize('delete', [Room::class, $hotel, $room]);

		$room->delete();

		return redirect()->route('admin.hotel.rooms.index', $hotel)->with('flash', [
			'message' => __('Room successfully deleted.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	public function restore(Hotel $hotel, Room $room): RedirectResponse
	{
		$this->authorize('restore', [Room::class, $hotel, $room]);

		$room->restore();

		return redirect()->route('admin.hotel.rooms.index', $hotel)->with('flash', [
			'message' => __('Room successfully restored.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}
}

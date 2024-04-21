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
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Hotel $hotel): Response
	{
		$this->authorize('create', [ExternalBooking::class, $hotel]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, Hotel $hotel, Room $room): RedirectResponse
	{
		$this->authorize('create', [ExternalBooking::class, $hotel, $room]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Hotel $hotel, Room $room, ExternalBooking $externalBooking): Response
	{
		$this->authorize('update', [ExternalBooking::class, $hotel, $room, $externalBooking]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Hotel $hotel, Room $room, ExternalBooking $externalBooking): RedirectResponse
	{
		$this->authorize('update', [ExternalBooking::class, $hotel, $room, $externalBooking]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Hotel $hotel, Room $room, ExternalBooking $externalBooking): RedirectResponse
	{
		$this->authorize('delete', [ExternalBooking::class, $hotel, $room, $externalBooking]);
	}
}

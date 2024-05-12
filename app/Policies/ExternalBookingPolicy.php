<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Policies;

use App\Models\Admin;
use App\Models\ExternalBooking;
use App\Models\Hotel;
use App\Models\Room;

class ExternalBookingPolicy
{
	public function before(Admin $admin, string $ability): ?bool
	{
		if ($admin->is_super_admin)
			return true;
		return null;
	}

	/**
	 * Determine whether the user can view any models.
	 */
	public function viewAny(Admin $admin, Hotel $hotel): bool
	{
		return $admin->hotels->contains($hotel);
	}

	/**
	 * Determine whether the user can create models.
	 */
	public function create(Admin $admin, Hotel $hotel): bool
	{
		return $admin->hotels->contains($hotel);
	}

	/**
	 * Determine whether the user can update the model.
	 */
	public function update(Admin $admin, Hotel $hotel, Room $room, ExternalBooking $externalBooking): bool
	{
		return $externalBooking->room_id === $room->id
			&& $room->hotel_id === $hotel->id
			&& $admin->hotels->contains($hotel);
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(Admin $admin, Hotel $hotel, Room $room, ExternalBooking $externalBooking): bool
	{
		return $externalBooking->room_id === $room->id
			&& $room->hotel_id === $hotel->id
			&& $admin->hotels->contains($hotel);
	}
}

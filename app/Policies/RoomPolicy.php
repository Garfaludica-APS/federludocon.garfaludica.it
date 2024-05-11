<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Hotel;
use App\Models\Room;

class RoomPolicy
{
	public function before(Admin $admin, string $ability): ?bool
	{
		if (!in_array($ability, ['restore', 'delete']) && $admin->is_super_admin)
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
	public function update(Admin $admin, Hotel $hotel, Room $room): bool
	{
		return false && $room->hotel_id === $hotel->id && $admin->hotels->contains($hotel);
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(Admin $admin, Hotel $hotel, Room $room): bool
	{
		return !$room->trashed() && ($admin->is_super_admin
			|| ($room->hotel_id === $hotel->id && $admin->hotels->contains($hotel)));
	}

	/**
	 * Determine whether the user can restore the model.
	 */
	public function restore(Admin $admin, Hotel $hotel, Room $room): bool
	{
		return $room->trashed() && ($admin->is_super_admin
			|| ($room->hotel_id === $hotel->id && $admin->hotels->contains($hotel)));
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 */
	public function forceDelete(Admin $admin, Hotel $hotel, Room $room): bool
	{
		return false;
	}
}

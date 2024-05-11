<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Hotel;
use App\Models\Meal;
use Illuminate\Auth\Access\Response;

class MealPolicy
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
	public function update(Admin $admin, Hotel $hotel, Meal $meal): bool
	{
		return $meal->hotel_id === $hotel->id && $admin->hotels->contains($hotel);
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(Admin $admin, Hotel $hotel, Meal $meal): bool
	{
		return !$meal->trashed() && ($admin->is_super_admin
			|| ($meal->hotel_id === $hotel->id && $admin->hotels->contains($hotel)));
	}

	/**
	 * Determine whether the user can restore the model.
	 */
	public function restore(Admin $admin, Hotel $hotel, Meal $meal): bool
	{
		return $meal->trashed() && ($admin->is_super_admin
			|| ($meal->hotel_id === $hotel->id && $admin->hotels->contains($hotel)));
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 */
	public function forceDelete(Admin $admin, Hotel $hotel, Meal $meal): bool
	{
		return false;
	}
}

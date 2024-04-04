<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
	public function before(Admin $admin, string $ability): bool|null
	{
		if ($ability === 'delete')
			return null;
		if ($admin->is_super_admin)
			return true;
		return null;
	}

	public function viewAny(Admin $admin): bool
	{
		return true;
	}

	public function delete(Admin $admin, Admin $target): bool
	{
		if ($admin->is_super_admin && !$target->is_super_admin)
			return true;
		if ($admin->id >= $target->id)
			return false;
		if ($admin->is_super_admin)
			return true;
		if ($admin->invitedAdmins()->get()->contains($target))
			return true;
		return false;
	}
}

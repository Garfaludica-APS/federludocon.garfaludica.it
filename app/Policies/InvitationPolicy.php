<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Invitation;

class InvitationPolicy
{
	public function before(Admin $admin): bool|null
	{
		if ($admin->is_super_admin)
			return true;
		return null;
	}

	public function create(Admin $admin): bool
	{
		return true;
	}

	public function store(Admin $admin): bool
	{
		return true;
	}

	public function delete(Admin $admin, Invitation $invitation): bool
	{
		return $admin->is_super_admin || $admin->id === $invitation->created_by;
	}
}

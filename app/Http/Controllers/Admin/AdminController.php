<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Hotel;
use App\Models\Invitation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class AdminController extends Controller
{
	use AuthorizesRequests;

	/**
	 * Display a listing of the resource.
	 */
	public function index(): Response
	{
		$this->authorize('viewAny', Admin::class);
		$admin = Auth::user();
		return inertia('Admin/Admins', [
			'admins' => Admin::where('id', '<>', Auth::id())->get()->transform(fn ($cur) => [
				'id' => $cur->id,
				'username' => $cur->username,
				'email' => $cur->email,
				'is_super_admin' => $cur->is_super_admin,
				'created_at' => $cur->created_at,
				'updated_at' => $cur->updated_at,
				'inviter' => $cur->inviter,
				'hotels' => $cur->hotels()->get(),
				'can' => [
					'delete' => $admin->can('delete', $cur),
				],
			]),
			'invitations' => Invitation::whereNull('accepted_at')->with('creator:id,username')->get()->transform(fn ($invitation) => [
				'id' => $invitation->id,
				'email' => $invitation->email,
				'is_super_admin' => $invitation->is_super_admin,
				'creator' => $invitation->creator,
				'created_at' => $invitation->created_at,
				'accepted_at' => $invitation->accepted_at,
				'can' => [
					'delete' => $admin->can('delete', $invitation),
				],
			]),
			'canCreateInvitations' => $admin->can('create', Invitation::class),
			'hotels' => Hotel::all(),
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Admin $admin): RedirectResponse
	{
		$this->authorize('delete', $admin);
		$admin->delete();
		return redirect()->route('admin.admins.index')->with('flash', [
			'message' => __('Admin \':admin\' successfully deleted.', ['admin' => $admin->username]),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}
}

<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class AdminController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): Response
	{
		return inertia('Admin/Admins/Index', [
			'admin' => Auth::user(),
			'admins' => Admin::where('id', '<>', Auth::id())->get(),
			'invitations' => Invitation::whereNull('accepted_at')->with('admin:id,username')->get(),
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Admin $admin): RedirectResponse
	{
		// TODO
		$this->authorize('delete', $admin);
		$admin->delete();
		return to_route('admin.admins.index');
	}
}

<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\Invite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Response;

class AdminController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): Response
	{
		return inertia('Admin/Admins/Index', [
			'admin' => auth()->user(),
			'admins' => Admin::all(),
			'invites' => Invite::whereNull('accepted_at')->with('admin:id,username')->get(),
		]);
	}

	public function create(Invite $invite, string $token): Response
	{
		if ($invite->token !== $token)
			return inertia('Auth/InvalidToken');
		return inertia('Admin/Admins/Create', [
			'invite' => $invite,
		]);
	}

	public function store(Request $request, Invite $invite): RedirectResponse
	{
		$validated = $request->validate([
			'username' => 'required|string|min:3|max:32|unique:admins',
			'password' => ['required', Password::defaults(), 'confirmed'],
		]);
		$admin = Admin::factory()->create([
			...$validated,
			'email' => $invite->email,
			'invite_id' => $invite->id,
			'is_super_admin' => $invite->is_super_admin,
			'remember_token' => null,
		]);
		Auth::login($admin);
		$request->session()->regenerate();
		return to_route('admin.dashboard');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Admin $admin): Response
	{
		return inertia('Admin/Admins/Edit', [
			'admin' => $admin,
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
	{
		$this->authorize('update', $admin);
		// TODO: validate
		$validated = [];
		$admin->update($validated);
		return to_route('admin.admins.index');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Admin $admin): RedirectResponse
	{
		$this->authorize('delete', $admin);
		$admin->delete();
		return to_route('admin.admins.index');
	}
}

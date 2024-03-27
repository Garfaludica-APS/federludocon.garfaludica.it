<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminInvitation;
use App\Models\Admin;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Response;

class InvitationController extends Controller
{
	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): void
	{
		// TODO: check for expired invitations
		$validated = $request->validate([
			'email' => 'required|email:strict,dns,spoof|max:254|unique:invitations|unique:admins',
		]);
		$token = Str::random(60);
		$invitation = $request->user()->invitations()->create([
			...$validated,
			'token' => $token,
		]);
		Mail::to($validated['email'])->send(new AdminInvitation($invitation));
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invitation $invitation): RedirectResponse
	{
		// TODO
		$this->authorize('delete', $invitation);
		$invitation->delete();
		return to_route('admin.admins.index');
	}

	public function accept(Request $request, Invitation $invitation): RedirectResponse
	{
		$validated = $request->validate([
			'username' => 'required|string|min:3|max:32|unique:admins|unique:admins,email',
			'password' => ['required', 'confirmed', Password::defaults()],
		]);
		DB::transaction(function () use ($invitation, $validated) {
			$admin = new Admin([
				...$validated,
				'email' => $invitation->email,
				'is_super_admin' => $invitation->is_super_admin,
			]);
			$admin->setRememberToken(Str::random(60));
			$invitation->accepted_at = now();
			$invitation->invitee()->save($admin);
			//$invitation->save(); // TODO: check if this is necessary
			Auth::login($admin, true);
		});
		$request->session()->regenerate();
		return to_route('admin.dashboard');
	}

	public function acceptForm(Invitation $invitation, string $token): Response
	{
		if ($invitation->token !== $token)
			abort(404);

		return inertia('Auth/Registration', [
			'invitation' => $invitation,
			'token' => $token,
		]);
	}
}

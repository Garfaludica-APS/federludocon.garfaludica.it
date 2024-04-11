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
use App\Rules\ValidTokenWithEmail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Response;

class InvitationController extends Controller
{
	use AuthorizesRequests;

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): RedirectResponse
	{
		$this->authorize('store', Invitation::class);
		$validated = $request->validate([
			'email' => 'required|email:strict,dns,spoof|max:254|unique:admins',
			'superAdmin' => 'boolean',
			'selectedHotels' => 'array',
			'selectedHotels.*.id' => 'exists:hotels,id',
		]);
		$invitations = Invitation::where('email', $validated['email'])->get();

		foreach ($invitations as $invitation) {
			if ($invitation->created_at > now()->subMinutes(60 * 24 * 3))
				return back()->withErrors([
					'email' => __('There\'s already a pending invitation for this email address.'),
				])->onlyInput('email');

			$invitation->delete();
		}

		$superAdmin = $validated['superAdmin'] ?? false;
		$admin = Auth::user();
		$validHotelIds = $admin->hotels()->pluck('id')->toArray();
		if ($superAdmin && !$admin->is_super_admin)
			$superAdmin = false;
		$token = Str::random(60);
		$hotelIds = [];
		if (!$superAdmin && isset($validated['selectedHotels']))
			foreach ($validated['selectedHotels'] as $hotel)
				if (($admin->is_super_admin || \in_array($hotel['id'], $validHotelIds, true)) && !\in_array($hotel['id'], $hotelIds, true))
					$hotelIds[] = $hotel['id'];
		$invitation = $request->user()->createdInvitations()->create([
			'email' => $validated['email'],
			'token' => $token,
			'is_super_admin' => $superAdmin,
			'hotels' => $hotelIds,
		]);
		Mail::to($validated['email'])->queue(new AdminInvitation($invitation, $token));
		return redirect()->route('admin.admins.index')->with('flash', [
			'message' => __('Invitation sent to :email.', ['email' => $validated['email']]),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invitation $invitation): RedirectResponse
	{
		$this->authorize('delete', $invitation);
		$invitation->delete();
		return redirect()->route('admin.admins.index')->with('flash', [
			'message' => __('Invitation successfully deleted.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	public function accept(Request $request, Invitation $invitation): RedirectResponse
	{
		$validated = Validator::make($request->all(), [
			'token' => ['required', 'string', new ValidTokenWithEmail(
				'invitations',
				60 * 24 * 3,
			)],
			'email' => 'required|email:strict,dns,spoof|max:254|unique:admins',
			'username' => 'required|string|min:3|max:32|unique:admins|unique:admins,email',
			'password' => ['required', 'confirmed', Password::defaults()],
		])->after(static function($validator) use ($invitation): void {
			if ($validator->getValue('email') !== $invitation->email)
				$validator->errors()->add('email', __('The email does not match the invitation.'));
			if (!Hash::check($validator->getValue('token'), $invitation->token))
				$validator->errors()->add('token', __('The token is invalid.'));
		})->validate();

		DB::transaction(static function() use ($invitation, $validated): void {
			$admin = new Admin([
				...$validated,
				'email' => $invitation->email,
				'is_super_admin' => $invitation->is_super_admin,
			]);
			$admin->setRememberToken(Str::random(60));
			$invitation->accepted_at = now();
			$invitation->invitee()->save($admin);
			$invitation->save();
			$admin->refresh();
			$admin->hotels()->sync($invitation->hotels ?? []);
			Auth::login($admin, true);
		});
		$request->session()->regenerate();
		return to_route('admin.dashboard');
	}

	public function acceptForm(Request $request, Invitation $invitation, string $token): Response
	{
		$validator = Validator::make(
			[
				'email' => $request->input('email'),
				'token' => $token,
			],
			[
				'email' => 'required|email|unique:admins',
				'token' => ['required', 'string', new ValidTokenWithEmail(
					'invitations',
					60 * 24 * 3,
				)],
			]
		)->after(static function($validator) use ($invitation): void {
			if ($validator->getValue('email') !== $invitation->email)
				$validator->errors()->add('email', __('The email does not match the invitation.'));
			if (!Hash::check($validator->getValue('token'), $invitation->token))
				$validator->errors()->add('token', __('The token is invalid.'));
		});
		if ($validator->fails())
			abort(404);

		return inertia('Auth/Registration', [
			'invitation' => $invitation,
			'token' => $token,
			'email' => $invitation->email,
		]);
	}
}

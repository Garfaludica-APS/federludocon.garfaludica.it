<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InviteController extends Controller
{
	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): void
	{
		$validated = $request->validate([
			'email' => 'required|email:strict,dns,spoof|max:254|unique:invites|unique:App\Models\Admin',
		]);
		$request->user()->invites()->create([
			...$validated,
			'token' => Str::random(60),
		]);
		//return to_route('admins.index');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Invite $invite): RedirectResponse
	{
		$this->authorize('delete', $invite);
		$invite->delete();
		return to_route('admin.admins.index');
	}

	public function accept(Invite $invite): RedirectResponse
	{
		// TODO
		return to_route('admin.index');
	}
}

<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Mail\StartBooking;
use App\Models\Booking;
use Biscolab\ReCaptcha\Facades\ReCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Inertia\Response;

class BookController extends Controller
{
	public function show(): Response
	{
		$scriptTag = ReCaptcha::htmlScriptTagJsApi([
			'lang' => App::isLocale('it') ? 'it' : 'en',
		]);
		preg_match('/src="([^"]+)"/', $scriptTag, $m);
		$src = $m[1];
		return inertia('Book', [
			'recaptchaScriptTagSrc' => $src,
			'recaptchaFormSnippet' => ReCaptcha::htmlFormSnippet(),
		]);
	}

	public function submit(Request $request): RedirectResponse
	{
		$validated = $request->validate([
			'email' => 'required|max:254|email:strict,dns,spoof',
			'g_recaptcha_response' => 'required|' . recaptchaRuleName(),
		]);

		$booking = Booking::where('email', $validated['email'])
			->where('state', BookingState::START)->first();

		if (!$booking)
			$booking = Booking::create([
				...$validated,
				'state' => BookingState::START,
			]);

		Mail::to($validated['email'])->send(new StartBooking($booking)); // TODO: queue

		return redirect()->route('book')->with('flash', [
			'message' => __('We have sent an email to :email with detailed instructions to proceed with your booking. Please check your inbox. If you didn\'t receive the email, you can request a new email in 3 minutes.', [
				'email' => $validated['email'],
			]),
			'style' => 'success',
			'location' => 'modal',
		]);
	}
}

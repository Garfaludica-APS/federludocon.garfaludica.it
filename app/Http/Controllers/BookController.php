<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Mail\StartBooking;
use App\Models\Booking;
use App\RateLimiters\StartBookingRateLimiter;
use Biscolab\ReCaptcha\Facades\ReCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Inertia\Response;

class BookController extends Controller
{
	protected StartBookingRateLimiter $limiter;

	public function __construct(StartBookingRateLimiter $limiter)
	{
		$this->limiter = $limiter;
	}

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
			'state' => 'missing',
			'expires_at' => 'missing',
		]);

		if ($this->limiter->tooManyAttempts($request)) {
			$seconds = $this->limiter->availableIn($request);
			return redirect()->back()->withErrors([
				'global' => __(
					'Too many attempts. Please try again in :seconds seconds.',
					['seconds' => $seconds]
				),
				])->onlyInput('email');
		}

		$this->limiter->increment($request);

		$booking = Booking::where('email', $validated['email'])
			->where('state', BookingState::START)->first();

		if (!$booking)
			$booking = Booking::create([
				...$validated,
				'state' => BookingState::START,
				'expires_at' => now()->addHours(2),
			]);

		Mail::to($validated['email'])->send(new StartBooking($booking)); // TODO: queue

		return redirect()->back()->with('flash', [
			'message' => __('We have sent an email to :email with detailed instructions to proceed with your booking. Please check your inbox. If you didn\'t receive the email, you can request a new email in 3 minutes.', [
				'email' => $validated['email'],
			]),
			'style' => 'success',
			'location' => 'modal',
		]);
	}
}

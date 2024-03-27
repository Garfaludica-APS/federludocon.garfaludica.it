<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\RateLimiters\LoginRateLimiter;
use App\Rules\ValidToken;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordValidation;
use Inertia\Response;

class AuthController extends Controller
{
	protected LoginRateLimiter $limiter;

	public function __construct(LoginRateLimiter $limiter)
	{
		$this->limiter = $limiter;
	}

	public function authenticate(LoginRequest $request): RedirectResponse
	{
		$credentials = $request->credentials();

		if ($this->limiter->tooManyAttempts($request)) {
			$seconds = $this->limiter->availableIn($request);
			return redirect()->back()->withErrors([
				'global' => __(
					'Too many login attempts. Please try again in :seconds seconds.',
					['seconds' => $seconds]
				),
			])->onlyInput('username', 'remember');
		}

		$this->limiter->increment($request);

		if (Auth::attempt($credentials, $request->boolean('remember'))) {
			$request->session()->regenerate();
			$this->limiter->clear($request);
			return redirect()->intended(route('admin.dashboard'));
		}

		return redirect()->back()->withErrors([
			'global' => __('These credentials do not match our records.'),
		])->onlyInput('username', 'remember');
	}

	public function login(Request $request): RedirectResponse|Response
	{
		return inertia('Auth/Login');
	}

	public function logout(Request $request): RedirectResponse
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		$prefix = App::isLocale('it') ? '' : App::currentLocale() . '.';
		return to_route("{$prefix}auth.login");
	}

	public function forgotPassword(): Response
	{
		return inertia('Auth/ForgotPassword');
	}

	public function sendPasswordResetLink(Request $request): RedirectResponse
	{
		$request->validate(['email' => 'required|email']);

		$status = Password::sendResetLink(
			$request->only('email')
		);

		if ($status === Password::RESET_LINK_SENT) {
			$lang = App::isLocale('it') ? '' : App::currentLocale() . '.';
			return redirect()->route("{$lang}auth.login")->with('flash', [
				'message' => __($status),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'success',
			]);
		}
		return redirect()->back()->withErrors(['email' => __($status)]);
	}

	public function resetPassword(Request $request, string $token): Response
	{
		$request->validate([
			'token' => ['required', 'string', new ValidToken(
				config('auth.passwords.admins.table'),
				config('auth.passwords.admins.expire'),
			)],
		]);
		// $tokens = DB::table(config('auth.passwords.admins.table'))
		// 	->select('token')
		// 	->where('created_at', '>', now()->subMinutes(config('auth.passwords.admins.expire')))
		// 	->get();
		// $tokenValid = false;
		// foreach ($tokens as $t)
		// 	if (Hash::check($token, $t->token)) {
		// 		$tokenValid = true;
		// 		break;
		// 	}
		// if (!$tokenValid)
		// 	abort(404);

		return inertia('Auth/ResetPassword', ['token' => $token]);
	}

	public function updatePassword(Request $request): RedirectResponse
	{
		$request->validate([
			'token' => 'required|string',
			'email' => 'required|email',
			'password' => ['required', 'confirmed', PasswordValidation::defaults()],
		]);

		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function (Admin $admin, string $password) {
				$admin->forceFill([
					'password' => Hash::make($password),
				])->setRememberToken(Str::random(60));

				$admin->save();

				event(new PasswordReset($admin));
			}
		);

		if ($status === Password::PASSWORD_RESET) {
			$lang = App::isLocale('it') ? '' : App::currentLocale() . '.';
			return redirect()->route("{$lang}auth.login")->with('flash', [
				'message' => __($status),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'success',
			]);
		}
		return redirect()->back()->withErrors(['global' => __($status)]);
	}
}

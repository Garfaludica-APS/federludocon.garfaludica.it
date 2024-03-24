<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return !Auth::check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'username' => 'required|string|min:3|max:32',
			'password' => ['required', Password::defaults()],
			'remember' => 'boolean',
		];
	}

	/**
	 * @return array<string, mixed>
	 */
	public function credentials(): array
	{
		$username = $this->input('username');
		if ($this->isEmail($username))
			return [
				'email' => $username,
				'password' => $this->input('password'),
			];
		return $this->only('username', 'password');
	}

	private function isEmail(string $param): bool
	{
		$factory = $this->container->make(ValidationFactory::class);
		return $factory->make(
			['username' => $param],
			['username' => 'email']
		)->passes();
	}
}

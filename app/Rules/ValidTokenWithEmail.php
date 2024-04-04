<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ValidTokenWithEmail implements DataAwareRule, ValidationRule
{
	/**
	 * Run the validation rule.
	 *
	 * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
	 */
	protected string $table;

	protected ?string $column;

	protected string $emailColumn = 'email';

	protected int $expireMinutes = 60;

	protected string $email;

	public function __construct(
		string $table,
		int $expireMinutes = 60,
		?string $column = null,
		string $emailColumn = 'email'
	) {
		$this->table = $table;
		$this->column = $column;
		$this->expireMinutes = $expireMinutes;
		$this->emailColumn = $emailColumn;
	}

	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		$token = DB::table($this->table)
			->select($this->column ?? $attribute)
			->where($this->emailColumn, $this->email)
			->where('created_at', '>', now()->subMinutes($this->expireMinutes))
			->whereNull('accepted_at')
			->first();
		if ($token === null || !Hash::check($value, $token->{$this->column ?? $attribute}))
			$fail(__('The :attribute is invalid.', ['attribute' => $attribute]));
	}

	public function setData(array $data): static
	{
		$this->email = $data[$this->emailColumn];
		return $this;
	}
}

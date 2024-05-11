<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInfo extends Model
{
	use HasFactory;

	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'address_line_1',
		'address_line_2',
		'city',
		'state',
		'postal_code',
		'country_code',
		'phone',
		'tax_id',
		'phone_country_code',
	];

	public function booking(): BelongsTo
	{
		return $this->hasOne(Booking::class);
	}

	protected function firstName(): Attribute
	{
		return Attribute::make(
			set: static function(string $value) {
				$arr = explode(' ', trim($value));
				$res = '';

				foreach ($arr as $val)
					$res .= ucfirst($val) . ' ';
				return trim($res);
			},
		);
	}

	protected function lastName(): Attribute
	{
		return Attribute::make(
			set: static function(string $value) {
				$arr = explode(' ', trim($value));
				$res = '';

				foreach ($arr as $val)
					$res .= ucfirst($val) . ' ';
				return trim($res);
			},
		);
	}

	protected function email(): Attribute
	{
		return Attribute::make(
			set: static fn(string $value) => mb_strtolower(trim($value)),
		);
	}

	protected function addressLine1(): Attribute
	{
		return Attribute::make(
			set: static fn(string $value) => ucfirst(trim($value)),
		);
	}

	protected function addressLine2(): Attribute
	{
		return Attribute::make(
			set: static fn(?string $value) => $value === null ? null : ucfirst(trim($value)),
		);
	}

	protected function city(): Attribute
	{
		return Attribute::make(
			set: static fn(string $value) => ucfirst(trim($value)),
		);
	}

	protected function state(): Attribute
	{
		return Attribute::make(
			set: static fn(string $value) => ucfirst(trim($value)),
		);
	}

	protected function countryCode(): Attribute
	{
		return Attribute::make(
			set: static fn(string $value) => mb_strtoupper(trim($value)),
		);
	}

	protected function taxId(): Attribute
	{
		return Attribute::make(
			set: static fn(string $value) => mb_strtoupper(str_replace(' ', '', trim($value))),
		);
	}
}

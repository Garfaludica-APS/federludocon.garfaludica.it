<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invitation extends Model
{
	use HasFactory;
	use HasUlids;
	public const UPDATED_AT = null;
	protected $fillable = [
		'email',
		'token',
		'is_super_admin',
		'hotels',
	];
	protected $hidden = [
		'token',
	];

	public function creator(): BelongsTo
	{
		return $this->belongsTo(Admin::class, 'created_by');
	}

	public function invitee(): HasOne
	{
		return $this->hasOne(Admin::class, 'invitation_id');
	}

	public function addHotel(Hotel|int $hotel): void
	{
		if ($this->hasHotel($hotel))
		return;
		if ($hotel instanceof Hotel)
			$hotel = $hotel->id;
		$this->hotels[] = $hotel;
	}

	public function removeHotel(Hotel|int $hotel): void
	{
		if (!$this->hasHotel($hotel))
		return;
		if ($hotel instanceof Hotel)
			$hotel = $hotel->id;
		$this->hotels = array_diff($this->hotels, [$hotel]);
	}

	public function hasHotel(Hotel|int $hotel): bool
	{
		if ($hotel instanceof Hotel)
			$hotel = $hotel->id;
		return \in_array($hotel, $this->hotels, true);
	}

	protected function casts(): array
	{
		return [
			'token' => 'hashed',
			'is_super_admin' => 'boolean',
			'hotels' => 'array',
		];
	}

	protected function email(): Attribute
	{
		return Attribute::make(
			set: static fn(string $value) => mb_strtolower($value),
		);
	}
}

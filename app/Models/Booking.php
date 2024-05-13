<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use App\Enums\BookingState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class Booking extends Model
{
	use HasFactory;
	use HasUuids;
	use SoftDeletes;

	protected $fillable = [
		'email',
		'state',
		'expires_at',
	];

	public function getSignedUrl(): string
	{
		return URL::temporarySignedRoute('booking.start', $this->expires_at, ['booking' => $this]);
	}

	public function getModifyUrl(): string
	{
		return URL::signedRoute('booking.manage', ['booking' => $this]);
	}

	public function billingInfo(): HasOne
	{
		return $this->hasOne(BillingInfo::class);
	}

	public function rooms(): HasMany
	{
		return $this->hasMany(RoomReservation::class);
	}

	public function meals(): HasMany
	{
		return $this->hasMany(MealReservation::class);
	}

	protected function casts(): array
	{
		return [
			'state' => BookingState::class,
			'expires_at' => 'datetime',
		];
	}
}

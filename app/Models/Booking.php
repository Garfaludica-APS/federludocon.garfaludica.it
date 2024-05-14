<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use App\Enums\BookingState;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
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

	protected function total(): Attribute
	{
		return Attribute::make(
			get: function (?double $value): double|float {
				return $this->rooms()->sum('price') + $this->meals()->sum('price') - $this->meals()->sum('discount');
			},
		)->shouldCache();
	}

	protected function firstCheckIn(): Attribute
	{
		return Attribute::make(
			get: function (mixed $value): Carbon|string {
				$room = $this->rooms()->orderBy('checkin', 'asc')->first()?->checkin;
				$meal = $this->meals()->orderBy('date', 'asc')->first()?->date;
				if (!$room) return $meal;
				if (!$meal) return $room;
				return $meal < $room ? $meal : $room;
			},
		)->shouldCache();
	}

	protected function lastCheckOut(): Attribute
	{
		return Attribute::make(
			get: function (mixed $value): Carbon|string {
				$room = $this->rooms()->orderBy('checkout', 'desc')->first()?->checkout;
				$meal = $this->meals()->orderBy('date', 'desc')->first()?->date;
				if (!$room) return $meal;
				if (!$meal) return $room;
				return $meal > $room ? $meal : $room;
			},
		)->shouldCache();
	}

	protected function hotels(): Attribute
	{
		return Attribute::make(
			get: function (?array $value): array {
				$hotels = Hotel::all();
				$res = [];
				foreach ($hotels as $hotel) {
					 if ($this->rooms()->whereRelation('room', 'hotel_id', $hotel->id)->exists()) {
						$res[] = $hotel;
						continue;
					 }
					 if ($this->meals()->whereRelation('meal', 'hotel_id', $hotel->id)->exists())
						$res[] = $hotel;
				}
				return $res;
			}
		)->shouldCache();
	}

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

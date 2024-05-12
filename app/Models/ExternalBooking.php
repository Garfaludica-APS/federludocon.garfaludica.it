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
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;

class ExternalBooking extends Model
{
	use HasFactory;

	protected $fillable = [
		'checkin',
		'checkout',
	];

	public function room(): BelongsTo
	{
		return $this->belongsTo(Room::class);
	}

	public function hotel(): HasOneThrough
	{
		return $this->hasOneThrough(Hotel::class, Room::class, 'id', 'id', 'room_id', 'hotel_id');
	}

	protected function checkin(): Attribute
	{
		return Attribute::make(
			get: static fn(string $value) => Carbon::parse($value, 'UTC')->setTimezone('Europe/Rome'),
			set: static fn(Carbon|string $value) => \is_string($value) ? Carbon::parse($value, 'Europe/Rome')->setTimezone('UTC')->format('Y-m-d H:i:s') : $value->setTimezone('UTC')->format('Y-m-d H:i:s'),
		);
	}

	protected function checkout(): Attribute
	{
		return Attribute::make(
			get: static fn(string $value) => Carbon::parse($value, 'UTC')->setTimezone('Europe/Rome'),
			set: static fn(Carbon|string $value) => \is_string($value) ? Carbon::parse($value, 'Europe/Rome')->setTimezone('UTC')->format('Y-m-d H:i:s') : $value->setTimezone('UTC')->format('Y-m-d H:i:s'),
		);
	}

	protected function casts(): array
	{
		return [
			// 'checkin' => 'datetime',
			// 'checkout' => 'datetime',
		];
	}
}

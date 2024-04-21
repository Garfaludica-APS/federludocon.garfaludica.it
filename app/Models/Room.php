<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
	use HasFactory;
	use SoftDeletes;

	protected $fillable = [
		'name',
		'description',
		'quantity',
		'buy_options',
		'checkin_time',
		'checkout_time',
	];

	public function hotel(): BelongsTo
	{
		return $this->belongsTo(Hotel::class);
	}

	public function externalBookings(): HasMany
	{
		return $this->hasMany(ExternalBooking::class);
	}

	public function reservations(): HasMany
	{
		return $this->hasMany(RoomReservation::class);
	}
}

<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
}

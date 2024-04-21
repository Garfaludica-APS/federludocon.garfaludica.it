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

class MealReservation extends Model
{
	use HasFactory;

	protected $fillable = [
		'date',
		'price',
		'quantity',
	];

	public function meal(): BelongsTo
	{
		return $this->belongsTo(Meal::class);
	}

	public function hotel(): HasOneThrough
	{
		return $this->hasOneThrough(Hotel::class, Meal::class, 'id', 'id', 'meal_id', 'hotel_id');
	}

	public function booking(): BelongsTo
	{
		return $this->belongsTo(Booking::class);
	}
}

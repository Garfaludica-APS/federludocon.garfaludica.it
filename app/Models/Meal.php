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

class Meal extends Model
{
	use HasFactory;
	use SoftDeletes;

	protected $fillable = [
		'type',
		'menu',
		'price',
		'meal_time',
		'reservable',
	];

	public function hotel(): BelongsTo
	{
		return $this->belongsTo(Hotel::class);
	}

	public function reservations(): HasMany
	{
		return $this->hasMany(MealReservation::class);
	}
}

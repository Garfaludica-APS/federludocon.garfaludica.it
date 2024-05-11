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
		'quantity',
		'price',
		'discount',
	];

	protected static function booted(): void
	{
		parent::booted();
		static::addGlobalScope('order', function ($builder) {
			$builder->orderBy('date', 'asc')
				->orderBy('meal_id', 'asc')
				->orderBy('price', 'desc');
		});
		static::saving(function (MealReservation $mealReservation) {
			if ($mealReservation->discount > $mealReservation->price)
				$mealReservation->discount = $mealReservation->price;
		});
	}

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

	protected function casts(): array
	{
		return [
			'date' => 'date',
			'price' => 'decimal:2',
			'discount' => 'decimal:2',
		];
	}
}

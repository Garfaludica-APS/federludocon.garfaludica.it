<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use App\Enums\MealType;
use App\Enums\Menu;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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

	protected function mealTime(): Attribute
	{
		return Attribute::make(
			get: static fn(string $value) => Carbon::parse($value, 'UTC')->setTimezone('Europe/Rome'),
			set: static fn(Carbon|string $value) => \is_string($value) ? Carbon::parse($value, 'Europe/Rome')->setTimezone('UTC')->format('H:i:s') : $value->setTimezone('UTC')->format('H:i:s'),
		);
	}

	protected function casts(): array
	{
		return [
			'type' => MealType::class,
			'menu' => Menu::class,
			'price' => 'decimal:2',
			// 'meal_time' => 'datetime:H:i',
			'reservable' => 'boolean',
		];
	}
}

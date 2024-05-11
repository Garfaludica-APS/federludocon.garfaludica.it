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

class RoomReservation extends Model
{
	use HasFactory;

	protected $fillable = [
		'buy_option_id',
		'people',
		'checkin',
		'checkout',
		'price',
	];

	protected $appends = [
		'buy_option',
	];

	protected static function booted(): void
	{
		parent::booted();
		static::addGlobalScope('order', function ($builder) {
			$builder->orderBy('checkin', 'asc')
				->orderBy('price', 'desc');

		});
	}

	protected function buyOption(): Attribute
	{
		return Attribute::make(
			get: function (?array $value, array $attributes): ?array {
				foreach ($this->room->buy_options as $buyOption) {
					if ($buyOption['id'] === $attributes['buy_option_id']) {
						return $buyOption;
					}
				}
				return null;
			}
		)->shouldCache();
	}

	public function room(): BelongsTo
	{
		return $this->belongsTo(Room::class);
	}

	public function hotel(): HasOneThrough
	{
		return $this->hasOneThrough(Hotel::class, Room::class, 'id', 'id', 'room_id', 'hotel_id');
	}

	public function booking(): BelongsTo
	{
		return $this->belongsTo(Booking::class);
	}

	protected function casts(): array
	{
		return [
			'checkin' => 'datetime',
			'checkout' => 'datetime',
			'price' => 'decimal:2',
		];
	}
}

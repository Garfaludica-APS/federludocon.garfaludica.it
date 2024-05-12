<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use App\Enums\BookingState;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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

	protected $appends = [
		'checkin_dates',
	];

	public function getBuyOption(int $id): ?array
	{
		foreach ($this->buy_options as $option) {
			if ($option['id'] === $id)
				return $option;
		}
		return null;
	}

	public function availableCheckouts(Carbon|string $checkin): array
	{
		if (\is_string($checkin))
			$checkin = Carbon::parse($checkin, 'Europe/Rome')->setTimezone('UTC');
		$checkinTime = $this->checkin_time->copy();
		$checkinTime->setTimezone('UTC');
		if ($checkin < Carbon::parse('2024-06-14', 'UTC')->setTimeFrom($checkinTime))
			return [];
		if ($checkin > Carbon::parse('2024-06-16', 'UTC')->setTimeFrom($checkinTime))
			return [];
		$dummy = $this->checkin_time->copy();
		$dummy->setTimezone('UTC');
		$dummy->setDateFrom($checkin);
		if (!$checkin->eq($dummy))
			return [];
		$checkoutTime = $this->checkout_time->copy();
		$checkoutTime->setTimezone('UTC');
		$checkouts = [
			Carbon::parse('2024-06-15', 'UTC')->setTimeFrom($checkoutTime),
			Carbon::parse('2024-06-16', 'UTC')->setTimeFrom($checkoutTime),
			Carbon::parse('2024-06-17', 'UTC')->setTimeFrom($checkoutTime),
		];
		$bo = $this->buy_options;
		$usePeople = \count($bo) === 1 && $bo[0]['people'] === 0;
		$available = [];

		foreach ($checkouts as $index => $checkout) {
			if ($checkout < $checkin)
			continue;
			$reservations = RoomReservation::where('room_id', $this->id)
				->where(static function($query) use ($checkin, $checkout): void {
					$query->whereRaw('? between `checkin` and `checkout` or ? between `checkin` and `checkout`', [$checkin, $checkout]);
				})->get();
			$count = 0;

			foreach ($reservations as $reservation)
				if (!\in_array($reservation->state, [BookingState::FAILED, BookingState::CANCELLED, BookingState::REFUND_REQUESTED, BookingState::REFUNDED]))
					$count += $usePeople ? $reservation->people : 1;
			if ($count >= $this->quantity)
			continue;
			$checkout->setTimezone('Europe/Rome');
			$available[] = [
				'value' => $checkout,
				'label' => $checkout->translatedFormat('D d M H:i'),
			];
		}
		return $available;
	}

	public function availableSlots(Carbon|string $checkin, Carbon|string $checkout): bool|int
	{
		if (\is_string($checkin))
			$checkin = Carbon::parse($checkin, 'Europe/Rome')->setTimezone('UTC');
		if (\is_string($checkout))
			$checkout = Carbon::parse($checkout, 'Europe/Rome')->setTimezone('UTC');
		if ($checkin >= $checkout)
			return false;
		if ($checkin < now()->startOfDay())
			return false;
		$checkinTime = $this->checkin_time->copy();
		$checkinTime->setTimezone('UTC');
		$checkoutTime = $this->checkout_time->copy();
		$checkoutTime->setTimezone('UTC');
		if ($checkin < Carbon::parse('2024-06-14', 'UTC')->setTimeFrom($checkinTime))
			return [];
		if ($checkin > Carbon::parse('2024-06-16', 'UTC')->setTimeFrom($checkinTime))
			return [];
		if ($checkout < Carbon::parse('2024-06-15', 'UTC')->setTimeFrom($checkoutTime))
			return [];
		if ($checkout > Carbon::parse('2024-06-17', 'UTC')->setTimeFrom($checkoutTime))
			return [];
		$dummy = $this->checkin_time->copy();
		$dummy->setTimezone('UTC');
		$dummy->setDateFrom($checkin);
		if (!$checkin->eq($dummy))
			return false;
		$dummy = $this->checkout_time->copy();
		$dummy->setTimezone('UTC');
		$dummy->setDateFrom($checkout);
		if (!$checkout->eq($dummy))
			return false;
		$reservations = RoomReservation::where('room_id', $this->id)
			->where(static function($query) use ($checkin, $checkout): void {
				$query->whereRaw('? between `checkin` and `checkout` or ? between `checkin` and `checkout`', [$checkin, $checkout]);
			})->get();
		$bo = $this->buy_options;
		$usePeople = \count($bo) === 1 && $bo[0]['people'] === 0;
		$count = 0;

		foreach ($reservations as $reservation)
			if (!\in_array($reservation->state, [BookingState::FAILED, BookingState::CANCELLED, BookingState::REFUND_REQUESTED, BookingState::REFUNDED]))
				$count += $usePeople ? $reservation->people : 1;
		return $this->quantity - $count;
	}

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

	protected static function booted(): void
	{
		parent::booted();
		static::saving(static function(Room $room): void {
			$maxId = 0;

			foreach ($room->buy_options as $index => $option) {
				if (!isset($option['id']))
				continue;
				if ($option['id'] > $maxId)
					$maxId = $option['id'];
			}
			$options = [];

			foreach ($room->buy_options as $index => $option) {
				if (!isset($option['id'])) {
					++$maxId;
					$options[] = array_merge($option, ['id' => $maxId]);
				} else {
					$options[] = $option;
				}
			}
			$room->buy_options = $options;
		});
	}

	protected function checkinDates(): Attribute
	{
		return Attribute::make(
			get: function(?array $value, array $attributes): array {
				$checkinTime = Carbon::parse($attributes['checkin_time'], 'UTC');
				$checkoutTime = Carbon::parse($attributes['checkout_time'], 'UTC');
				$checkins = [
					Carbon::parse('2024-06-14', 'UTC')->setTimeFrom($checkinTime),
					Carbon::parse('2024-06-15', 'UTC')->setTimeFrom($checkinTime),
					Carbon::parse('2024-06-16', 'UTC')->setTimeFrom($checkinTime),
				];
				$checkouts = [
					Carbon::parse('2024-06-15', 'UTC')->setTimeFrom($checkoutTime),
					Carbon::parse('2024-06-16', 'UTC')->setTimeFrom($checkoutTime),
					Carbon::parse('2024-06-17', 'UTC')->setTimeFrom($checkoutTime),
				];
				$bo = $this->castAttribute('buy_options', $attributes['buy_options']);
				$usePeople = \count($bo) === 1 && $bo[0]['people'] === 0;
				$available = [];

				foreach ($checkins as $index => $checkin) {
					if ($checkin < now()->startOfDay())
					continue;
					$checkout = $checkouts[$index];
					$reservations = RoomReservation::where('room_id', $attributes['id'])
						->where(static function($query) use ($checkin, $checkout): void {
							$query->whereRaw('? between `checkin` and `checkout` or ? between `checkin` and `checkout`', [$checkin, $checkout]);
						})->get();
					$count = 0;

					foreach ($reservations as $reservation)
						if (!\in_array($reservation->state, [BookingState::FAILED, BookingState::CANCELLED, BookingState::REFUND_REQUESTED, BookingState::REFUNDED]))
							$count += $usePeople ? $reservation->people : 1;
					if ($count >= $attributes['quantity'])
					continue;
					$checkin->setTimezone('Europe/Rome');
					$available[] = [
						'value' => $checkin,
						'label' => $checkin->translatedFormat('D d M H:i'),
					];
				}
				return $available;
			}
		)->shouldCache();
	}

	protected function checkinTime(): Attribute
	{
		return Attribute::make(
			get: static fn(string $value) => Carbon::parse($value, 'UTC')->setTimezone('Europe/Rome'),
			set: static fn(Carbon|string $value) => \is_string($value) ? Carbon::parse($value, 'Europe/Rome')->setTimezone('UTC')->format('H:i:s') : $value->setTimezone('UTC')->format('H:i:s'),
		);
	}

	protected function checkoutTime(): Attribute
	{
		return Attribute::make(
			get: static fn(string $value) => Carbon::parse($value, 'UTC')->setTimezone('Europe/Rome'),
			set: static fn(Carbon|string $value) => \is_string($value) ? Carbon::parse($value, 'Europe/Rome')->setTimezone('UTC')->format('H:i:s') : $value->setTimezone('UTC')->format('H:i:s'),
		);
	}

	protected function casts(): array
	{
		return [
			'buy_options' => 'array',
			'name' => 'array',
			'description' => 'array',
			// 'checkin_time' => 'datetime:H:i',
			// 'checkout_time' => 'datetime:H:i',
		];
	}
}

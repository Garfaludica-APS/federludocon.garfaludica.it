<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Enums\MealType;
use App\Enums\Menu;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Meal;
use App\Models\MealReservation;
use App\Models\Room;
use App\Models\RoomReservation;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Response;

class BookingController extends Controller
{
	public function start(Request $request, Booking $booking): RedirectResponse
	{
		if (!$request->hasValidSignature())
			abort(404);

		if (\in_array($booking->state, [BookingState::PAYMENT, BookingState::COMPLETED, BookingState::FAILED, BookingState::CANCELLED, BookingState::REFUNDED]))
			abort(404);

		$request->session()->invalidate();

		$request->session()->put([
			'booking' => $booking->id,
			'editedRooms' => false,
		]);

		if ($booking->state === BookingState::SUMMARY)
			return redirect()->route('booking.summary');
		if ($booking->state === BookingState::BILLING)
			return redirect()->route('booking.billing');
		if ($booking->state === BookingState::MEALS)
			return redirect()->route('booking.meals');
		$booking->state = BookingState::ROOMS;
		$booking->expires_at = now()->addMinutes(config('gobcon.session_lifetime', 15));
		$booking->save();
		return redirect()->route('booking.rooms', [
			'booking' => $booking,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function rooms(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, [BookingState::ROOMS, BookingState::MEALS]);

		$prevState = $booking->state;
		$booking->state = BookingState::ROOMS;

		$booking->save();
		$booking->loadMissing('rooms', 'rooms.room', 'meals', 'meals.meal', 'billingInfo');

		$hotels = Hotel::with('rooms')->get();

		return inertia('Booking/Rooms', [
			'booking' => $booking,
			'hotels' => $hotels,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function availableCheckouts(Request $request, Room $room): JsonResponse
	{
		$validated = $request->validate([
			'checkin' => 'required|date',
		]);

		$booking = Booking::findOrFail($request->session()->get('booking'));
		$this->assertBookingState($request, $booking, BookingState::ROOMS);
		$booking->save();

		return response()->json([
			'checkouts' => $room->availableCheckouts($validated['checkin']),
			'sessionExpireSeconds' => floor(now()->diffInSeconds($booking->expires_at)),
		]);
	}

	public function maxPeople(Request $request, Room $room): JsonResponse
	{
		$validated = $request->validate([
			'checkin' => 'required|date',
			'checkout' => 'required|date|after:checkin',
		]);

		$booking = Booking::findOrFail($request->session()->get('booking'));
		$this->assertBookingState($request, $booking, BookingState::ROOMS);
		$booking->save();

		return response()->json([
			'maxPeople' => $room->availableSlots($validated['checkin'], $validated['checkout']),
			'sessionExpireSeconds' => floor(now()->diffInSeconds($booking->expires_at)),
		]);
	}

	public function addRoom(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::ROOMS);

		$booking->save();
		@\set_time_limit(55);

		$validated = $request->validate([
			'room' => 'required|exists:rooms,id',
			'buy_option' => 'required|integer|min:1',
			'checkin' => 'required|date',
			'checkout' => 'required|date|after:checkin',
			'people' => 'nullable|integer|min:1',
		]);

		$room = Room::findOrFail($validated['room']);
		$buyOption = $room->getBuyOption($validated['buy_option']);
		if ($buyOption === null)
			throw ValidationException::withMessages([
				'buy_option' => __('Invalid buy option.'),
			]);
		$multiBookable = $buyOption['people'] === 0;
		if ($multiBookable) {
			if ($validated['people'] === null)
				throw ValidationException::withMessages([
					'people' => __('This room requires to specify a number of people.'),
				]);

		} else {
			$validated['people'] = $buyOption['people'];
		}

		$lock = Cache::lock('bookings', 15);
		try {
			$lock->block(10);

			$slots = $room->availableSlots($validated['checkin'], $validated['checkout']);
			if ($slots === false)
				return redirect()->back()->with([
					'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
					'flash' => [
						'message' => __('An error has occured. Please, try again.'),
						'location' => 'toast-tc',
						'timeout' => 5000,
						'style' => 'error',
					],
				]);

			if ($slots < 1 || ($multiBookable && $slots < $validated['people']))
				return redirect()->back()->with([
					'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
					'flash' => [
						'message' => __('Sorry, the selected room is no more available.'),
						'location' => 'modal',
						'style' => 'error',
					],
				]);

			$people = $validated['people'];
			$reservation = null;
			if ($multiBookable) {
				$reservation = $booking->rooms()->where('room_id', $room->id)->where('buy_option_id', $validated['buy_option'])->where('checkin', $validated['checkin'])->where('checkout', $validated['checkout'])->first();
				$people = $reservation ? $reservation->people + $validated['people'] : $validated['people'];
			}

			$nights = round(Carbon::parse($validated['checkin'])->startOfDay()->diffInDays(Carbon::parse($validated['checkout'])->startOfDay()));
			$price = round($buyOption['price'] * $nights, 2);
			if ($multiBookable)
				$price = round($price * $people);
			if (!$reservation) {
				$reservation = new RoomReservation([
					'buy_option_id' => $validated['buy_option'],
					'checkin' => $validated['checkin'],
					'checkout' => $validated['checkout'],
					'people' => $people,
					'price' => round($price, 2),
				]);
				$reservation->room_id = $room->id;
				$reservation->booking_id = $booking->id;
			} else {
				$reservation->people = $people;
				$reservation->price = round($price, 2);
			}

			$includedMeals = $buyOption['included_meals'] = $buyOption['included_meals'] ?? [];
			$meals = Meal::whereIn('type', $includedMeals)->get();
			$mealReservations = $booking->meals()->with('meal')->get();

			$checkin = Carbon::parse($validated['checkin']);
			$checkout = Carbon::parse($validated['checkout']);

			$dates = [];
			for ($date = $checkin->copy()->startOfDay(); $date->lt($checkout); $date->addDay()) {
				$start = $date->isSameDay($checkin) ? $checkin->copy() : $date->copy();
				$end = $date->isSameDay($checkout) ? $checkout->copy() : $date->copy()->endOfDay();
				$dates[] = [
					$start,
					$end,
				];
			}

			$toSave = [];
			foreach ($dates as $range) {
				[$start, $end] = $range;
				$date = $start->copy()->startOfDay();
				foreach ($includedMeals as $type) {
					$type = MealType::from($type);
					$mealReservation = null;
					foreach ($mealReservations as $r) {
						if ($r->meal->type !== $type || !$r->date->copy()->setTimeFrom($r->meal->meal_time)->between($start, $end))
							continue;
						if ($type === MealType::BREAKFAST && $r->meal->hotel_id !== $room->hotel_id)
							continue;
						$mealReservation = $r;
						break;
					}
					$meal = null;
					if (!$mealReservation) {
						foreach ($meals as $m) {
							if ($m->type !== $type || !$m->meal_time->copy()->setDateFrom($date)->between($start, $end))
								continue;
							if ($type === MealType::BREAKFAST && $m->hotel_id !== $room->hotel_id)
								continue;
							$meal = $m;
							break;
						}
						if (!$meal)
							continue;
						$mealReservation = new MealReservation([
							'date' => $date,
							'quantity' => $people,
						]);
						$mealReservation->booking_id = $booking->id;
						$mealReservation->meal_id = $meal->id;
						$mealReservation->price = round($meal->price * $people, 2);
						$mealReservation->discount = $mealReservation->price;
					} else {
						$mealReservation->quantity += $people;
						$mealReservation->price = round($mealReservation->meal->price * $mealReservation->quantity, 2);
						$mealReservation->discount = round($mealReservation->discount + $mealReservation->meal->price * $people, 2);
					}
					$toSave[] = $mealReservation;
				}
			}

			DB::transaction(function () use ($booking, $reservation, $toSave) {
				foreach ($toSave as $r)
					$r->save();
				$reservation->save();
			});

		} catch (LockTimeoutException $e) {
			return redirect()->back()->with([
				'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
				'flash' => [
					'message' => __('An error has occured. Please, try again.'),
					'location' => 'toast-tc',
					'timeout' => 5000,
					'style' => 'error',
				],
			]);
		} finally {
			$lock?->release();
		}

		$request->session()->put('editedRooms', true);

		return redirect()->back()->with([
			'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
			'flash' => [
				'message' => __('Room successfully added to your order.'),
				'location' => 'toast-tc',
				'timeout' => 3000,
				'style' => 'success',
			],
		]);
	}

	public function deleteRoom(Request $request, Booking $booking, RoomReservation $reservation): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::ROOMS);

		@\set_time_limit(55);

		$reservation->load('room', 'room.hotel');

		$booking->save();

		if ($reservation->booking_id !== $booking->id)
			return redirect()->back()->with([
				'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
				'flash' => [
					'message' => __('An error has occured. Please, try again.'),
					'location' => 'toast-tc',
					'timeout' => 5000,
					'style' => 'error',
				],
			]);

		$buyOption = $reservation->room->getBuyOption($reservation->buy_option_id);
		if ($buyOption === null)
			return redirect()->back()->with([
				'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
				'flash' => [
					'message' => __('Invalid buy option.'),
					'location' => 'toast-tc',
					'timeout' => 5000,
					'style' => 'error',
				],
			]);

		$mealReservations = $booking->meals()->with('meal')->get();
		$includedMeals = $buyOption['included_meals'] = $buyOption['included_meals'] ?? [];

		$mealsByType = [];
		for ($date = $reservation->checkin->copy()->startOfDay(); $date->lt($reservation->checkout); $date->addDay()) {
			$mealsByType[$date->format('Y-m-d')] = [
				'breakfast' => [],
				'lunch' => [],
				'dinner' => [],
			];
		}
		foreach ($mealReservations as $r) {
			if (!$r->date->copy()->setTimeFrom($r->meal->meal_time)->between($reservation->checkin, $reservation->checkout))
				continue;
			if ($r->meal->type === MealType::BREAKFAST && $r->meal->hotel_id !== $reservation->room->hotel_id)
				continue;
			$included = false;
			foreach ($includedMeals as $type) {
				$type = MealType::from($type);
				if ($r->meal->type !== $type)
					continue;
				$included = true;
				break;
			}
			if (!$included)
				continue;
			$mealsByType[$r->date->format('Y-m-d')][$r->meal->type->value][] = $r;
		}

		$toSave = [];
		$toDelete = [];
		foreach ($mealsByType as $date => $meals) {
			foreach ($meals as $type => $reservations) {
				if (empty($reservations))
					continue;
				$totalQuantity = 0;
				foreach ($reservations as $r)
					$totalQuantity += $r->quantity;
				$alreadyRemoved = max($reservation->people - $totalQuantity, 0);
				$toRemove = $reservation->people - $alreadyRemoved;
				foreach ($reservations as $r) {
					if ($toRemove === 0)
						break;
					if ($r->quantity <= $toRemove) {
						$toDelete[] = $r;
						$toRemove -= $r->quantity;
						continue;
					}
					$r->quantity -= $toRemove;
					$r->price = round($r->meal->price * $r->quantity, 2);
					$r->discount = round($r->discount - $r->meal->price * $toRemove, 2);
					$toSave[] = $r;
					break;
				}
			}
		}

		DB::transaction(function () use ($reservation, $toSave, $toDelete) {
			foreach ($toSave as $r)
				$r->save();
			foreach ($toDelete as $r)
				$r->delete();
			$reservation->delete();
		});

		$request->session()->put('editedRooms', true);

		return redirect()->back()->with([
			'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
			'flash' => [
				'message' => __('Room successfully removed from your order.'),
				'location' => 'toast-tc',
				'timeout' => 3000,
				'style' => 'success',
			],
		]);
	}

	public function meals(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, [BookingState::ROOMS, BookingState::MEALS, BookingState::BILLING]);

		$prevState = $booking->state;
		$booking->state = BookingState::MEALS;

		$booking->save();

		if ($request->session()->get('editedRooms', false)) {
			@\set_time_limit(55);
			$request->session()->put('editedRooms', false);
			$this->addMissingMeals($booking);
		}

		$booking->loadMissing('rooms', 'rooms.room', 'meals', 'meals.meal', 'billingInfo');

		$hotels = Hotel::with('meals')->get();
		$freeMeals = $this->getFreeMeals($booking);

		return inertia('Booking/Meals', [
			'booking' => $booking,
			'hotels' => $hotels,
			'freeMeals' => $freeMeals,
			'dates' => array_keys($freeMeals),
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	private function addMissingMeals(Booking $booking): void
	{
		$lunch = Meal::whereRelation('hotel', 'name', 'Isera Refuge')->where('type', MealType::LUNCH)->where('menu', Menu::STANDARD)->first();
		$dinner = Meal::whereRelation('hotel', 'name', 'Panoramic Hotel')->where('type', MealType::DINNER)->where('menu', Menu::STANDARD)->first();

		$lunches = [];
		$dinners = [];
		$mealReservations = $booking->meals()->with('meal')->get();
		foreach ($mealReservations as $r) {
			if ($r->meal->type === MealType::LUNCH)
			$lunches[] = $r;
			if ($r->meal->type === MealType::DINNER)
			$dinners[] = $r;
		}

		$peoplePerDay = [
			Carbon::parse('2024-06-14', 'Europe/Rome')->midDay()->toString() => 0,
			Carbon::parse('2024-06-15', 'Europe/Rome')->midDay()->toString() => 0,
			Carbon::parse('2024-06-16', 'Europe/Rome')->midDay()->toString() => 0,
		];
		$reservedRooms = $booking->rooms;
		foreach ($reservedRooms as $r) {
			$people = $r->people;
			$checkinDay = $r->checkin->copy()->startOfDay();
			$checkoutDay = $r->checkout->copy()->endOfDay();
			foreach (array_keys($peoplePerDay) as $date) {
				if (!Carbon::parse($date, 'Europe/Rome')->between($checkinDay, $checkoutDay))
					continue;
				$peoplePerDay[$date] += $people;
			}
		}

		$toSave = [];
		foreach ($peoplePerDay as $day => $people) {
			if ($people === 0)
				continue;
			$day = Carbon::parse($day, 'Europe/Rome');
			$dayLunches = 0;
			$lunchReservation = null;
			foreach ($lunches as $l)
				if ($l->date->isSameDay($day)) {
					$dayLunches += $l->quantity;
					if ($l->meal->menu === Menu::STANDARD)
						$lunchReservation = $l;
				}
			$dayDinners = 0;
			$dinnerReservation = null;
			foreach ($dinners as $d)
				if ($d->date->isSameDay($day)) {
					$dayDinners += $d->quantity;
					if ($d->meal->menu === Menu::STANDARD)
						$dinnerReservation = $d;
				}
			if ($lunchReservation === null && $dayLunches < $people) {
				$lunchReservation = new MealReservation([
					'date' => $day,
					'quantity' => $people - $dayLunches,
					'price' => round($lunch->price * ($people - $dayLunches), 2),
					'discount' => 0,
				]);
			} elseif ($dayLunches < $people) {
				$lunchReservation->quantity += $people - $dayLunches;
				$lunchReservation->price = round($lunch->price * $lunchReservation->quantity, 2);
			}
			if ($dinnerReservation === null && $dayDinners < $people) {
				$dinnerReservation = new MealReservation([
					'date' => $day,
					'quantity' => $people - $dayDinners,
					'price' => round($dinner->price * ($people - $dayDinners), 2),
					'discount' => 0,
				]);
			} elseif ($dayDinners < $people) {
				$dinnerReservation->quantity += $people - $dayDinners;
				$dinnerReservation->price = round($dinner->price * $dinnerReservation->quantity, 2);
			}
			if ($dayLunches < $people) {
				$lunchReservation->booking_id = $booking->id;
				$lunchReservation->meal_id = $lunch->id;
				$toSave[] = $lunchReservation;
			}
			if ($dayDinners < $people) {
				$dinnerReservation->booking_id = $booking->id;
				$dinnerReservation->meal_id = $dinner->id;
				$toSave[] = $dinnerReservation;
			}
		}

		DB::transaction(function () use ($toSave) {
			foreach ($toSave as $r)
				$r->save();
		});
	}

	private function getFreeMeals(Booking $booking): array
	{
		$freeMeals = [
			Carbon::parse('2024-06-14', 'Europe/Rome')->startOfDay()->toString() => [
				'DISPLAY' => Carbon::parse('2024-06-14', 'Europe/Rome')->startOfDay()->translatedFormat('l j F'),
				'breakfast' => 0,
				'lunch' => 0,
				'dinner' => 0,
			],
			Carbon::parse('2024-06-15', 'Europe/Rome')->startOfDay()->toString() => [
				'DISPLAY' => Carbon::parse('2024-06-15', 'Europe/Rome')->startOfDay()->translatedFormat('l j F'),
				'breakfast' => 0,
				'lunch' => 0,
				'dinner' => 0,
			],
			Carbon::parse('2024-06-16', 'Europe/Rome')->startOfDay()->toString() => [
				'DISPLAY' => Carbon::parse('2024-06-16', 'Europe/Rome')->startOfDay()->translatedFormat('l j F'),
				'breakfast' => 0,
				'lunch' => 0,
				'dinner' => 0,
			],
		];

		$reservedRooms = $booking->rooms()->with('room')->get();
		$meals = Meal::all();

		foreach ($reservedRooms as $r) {
			$people = $r->people;
			$buyOption = $r->room->getBuyOption($r->buy_option_id);
			$includedMeals = $buyOption['included_meals'] = $buyOption['included_meals'] ?? [];
			$checkin = $r->checkin;
			$checkout = $r->checkout;

			foreach ($freeMeals as $date => $entry) {
				foreach ($includedMeals as $type) {
					$type = MealType::from($type);
					$m = $entry[$type->value];
					$meal = null;
					foreach ($meals as $ml) {
						if ($ml->type !== $type)
							continue;
						if ($type === MealType::BREAKFAST && $ml->hotel_id !== $r->room->hotel_id)
							continue;
						$meal = $ml;
						break;
					}
					if (!$meal)
						continue;
					$carbonDate = Carbon::parse($date, 'Europe/Rome')->setTimeFrom($meal->meal_time);

					if (!$carbonDate->between($checkin, $checkout))
						continue;

					$freeMeals[$date][$type->value] += $people;
				}
			}
		}

		return $freeMeals;
	}

	public function editMeals(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::MEALS);

		$booking->save();

		$validated = $request->validate([
			'date' => 'required|date_format:Y-m-d',
			'type' => 'required|string|in:lunch,dinner',
			'standard' => 'required|integer|min:0',
			'vegetarian' => 'required|integer|min:0',
			'vegan' => 'required|integer|min:0',
		]);

		$date = Carbon::parse($validated['date'], 'Europe/Rome')->midDay();
		$type = MealType::from($validated['type']);

		$meals = MealReservation::with('meal')->whereRelation('meal', 'type', $type)->where('date', $date->format('Y-m-d'))->get();
		$freeMeals = $this->getFreeMeals($booking);
		$toDelete = [];
		$toSave = [];
		$freeMealCount = 0;
		foreach ($freeMeals as $d => $entry) {
			if (!$date->isSameDay($d))
				continue;
			$freeMealCount = $entry[$type->value];
			break;
		}
		foreach ([Menu::STANDARD, Menu::VEGETARIAN, Menu::VEGAN] as $menu) {
			$meal = null;
			foreach ($meals as $m)
				if ($m->meal->menu === $menu) {
					$meal = $m;
					break;
				}
			if (!$meal && $validated[$menu->value] === 0)
				continue;
			if (!$meal) {
				$meal = new MealReservation([
					'date' => $date,
					'quantity' => 0,
					'price' => 0.0,
					'discount' => 0.0,
				]);
				$meal->booking_id = $booking->id;
				if ($type === MealType::LUNCH)
					$meal->meal_id = Meal::whereRelation('hotel', 'name', 'Isera Refuge')->where('type', MealType::LUNCH)->where('menu', $menu)->first()->id;
				elseif ($type === MealType::DINNER)
					$meal->meal_id = Meal::whereRelation('hotel', 'name', 'Panoramic Hotel')->where('type', MealType::DINNER)->where('menu', $menu)->first()->id;
			}
			switch ($meal->meal->menu) {
			case Menu::STANDARD:
				$meal->quantity = $validated['standard'];
				break;
			case Menu::VEGETARIAN:
				$meal->quantity = $validated['vegetarian'];
				break;
			case Menu::VEGAN:
				$meal->quantity = $validated['vegan'];
				break;
			}
			$meal->price = round($meal->meal->price * $meal->quantity, 2);
			if ($freeMealCount >= $meal->quantity) {
				$meal->discount = round($meal->meal->price * $meal->quantity, 2);
				$freeMealCount -= $meal->quantity;
			} else {
				$meal->discount = round($meal->meal->price * $freeMealCount, 2);
				$freeMealCount = 0;
			}

			if ($meal->quantity === 0)
				$toDelete[] = $meal;
			else
				$toSave[] = $meal;
		}

		DB::transaction(function () use ($toSave, $toDelete) {
			foreach ($toDelete as $r)
				$r->delete();
			foreach ($toSave as $r)
				$r->save();
		});

		return redirect()->back()->with([
			'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
			'flash' => [
				'message' => __('Order updated!'),
				'location' => 'toast-tc',
				'timeout' => 3000,
				'style' => 'success',
			],
		]);
	}

	public function storeNotes(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::MEALS);

		$validated = $request->validate([
			'notes' => 'nullable|string|max:4096',
		]);

		$booking->notes = $validated['notes'] === null ? null : trim($validated['notes']);
		$booking->state = BookingState::BILLING;
		$booking->save();

		$emptyOrder = DB::table('room_reservations')->select('booking_id')->where('booking_id', $booking->id)->union(DB::table('meal_reservations')->select('booking_id')->where('booking_id', $booking->id))->doesntExist();
		if ($emptyOrder)
			return redirect()->back()->with([
				'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
				'flash' => [
					'message' => __('You can not proceed with an empty order. Please, add some rooms or meals to the order first.'),
					'location' => 'modal',
					'style' => 'warning',
				],
			]);

		return redirect()->route('booking.billing', [
			'booking' => $booking,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function billing(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, [BookingState::BILLING, BookingState::SUMMARY]);

		$booking->state = BookingState::BILLING;
		$booking->save();

		$booking->loadMissing('rooms', 'rooms.room', 'meals', 'meals.meal', 'billingInfo');
		$hotels = Hotel::all();

		return inertia('Booking/Billing', [
			'booking' => $booking,
			'hotels' => $hotels,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function storeBilling(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::BILLING);

		$validated = $request->validate([
			'first_name' => 'required|string|min:1|max:100',
			'last_name' => 'required|string|min:1|max:100',
			'tax_id' => 'required|string|min:1|max:20',
			'address_line_1' => 'required|string|min:1|max:300',
			'address_line_2' => 'nullable|string|max:300',
			'city' => 'required|string|min:1|max:120',
			'state' => 'required|string|min:1|max:300',
			'postal_code' => 'required|string|min:1|max:60',
			'country_code' => 'required|string|size:2',
			'email' => 'missing',
			'phone' => 'nullable|string|max:15',
		]);

		if ($booking->has('billingInfo'))
			$booking->billingInfo()->update($validated);
		else
			$booking->billingInfo()->create([
				'email' => $booking->email,
				...$validated
			]);

		$booking->billingInfo()->updateOrCreate([], [
			'email' => $booking->email,
			...$validated
		]);

		$booking->state = BookingState::SUMMARY;
		$booking->save();

		return redirect()->route('booking.summary', [
			'booking' => $booking,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function summary(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, BookingState::SUMMARY);

		$booking->save();

		$booking->loadMissing('rooms', 'rooms.room', 'meals', 'meals.meal', 'billingInfo');
		$hotels = Hotel::all();

		return inertia('Booking/Summary', [
			'booking' => $booking,
			'hotels' => $hotels,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function createOrder(Request $request, Booking $booking): JsonResponse
	{
	}

	public function captureOrder(Request $request, Booking $booking): JsonResponse
	{
	}

	public function terminate(Request $request, Booking $booking): RedirectResponse
	{
		if (!$request->session()->has('booking')
			|| $request->session()->get('booking') !== $booking->id)
			return redirect()->route(app()->isLocale('it') ? 'home' : 'en.home');
		$booking->delete();
		$request->session()->invalidate();
		return redirect()->route(app()->isLocale('it') ? 'home' : 'en.home')->with('flash', [
			'message' => __('Sorry, your session has expired.'),
			'location' => 'modal',
			'style' => 'info',
		]);
	}

	public function resetOrder(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, [BookingState::ROOMS, BookingState::MEALS, BookingState::BILLING]);
		DB::transaction(function () use ($booking) {
			$booking->rooms()->delete();
			$booking->meals()->delete();
		});
		$booking->save();
		return redirect()->back()->with([
			'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
			'flash' => [
				'message' => __('Booking successfully reset.'),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'success',
			],
		]);
	}

	protected function assertBookingState(Request $request, Booking $booking, array|BookingState $state): void
	{
		if (!$request->session()->has('booking')
			|| $request->session()->get('booking') !== $booking->id)
				abort(404);
		if ($booking->expires_at->isPast()) {
			$request->session()->invalidate();
			$booking->delete();
			abort(410);
		}
		if (!\is_array($state))
			$state = [$state];
		$curState = $booking->state;
		if (!\in_array($booking->state, $state))
			abort(403);

		$booking->expires_at = now()->addMinutes(config('gobcon.session_lifetime', 15));
	}
}

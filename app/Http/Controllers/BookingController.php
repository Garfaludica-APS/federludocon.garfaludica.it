<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Enums\MealType;
use App\Enums\Menu;
use App\Mail\OrderReceipt;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Meal;
use App\Models\MealReservation;
use App\Models\Room;
use App\Models\RoomReservation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Response;
use Squire\Models\Country;
use Srmklive\PayPal\Services\PayPal;

class BookingController extends Controller
{
	public function start(Request $request, Booking $booking): RedirectResponse
	{
		if (!$request->hasValidSignature())
			abort(404);

		if (\in_array($booking->state, [BookingState::PAYMENT, BookingState::COMPLETED, BookingState::FAILED, BookingState::CANCELLED, BookingState::REFUND_REQUESTED, BookingState::REFUNDED]))
			abort(404);

		//$request->session()->invalidate();

		// $request->session()->put([
		// 	'booking' => $booking->id,
		// 	'editedRooms' => false,
		// ]);

		if ($booking->state === BookingState::SUMMARY)
			return redirect()->route('booking.summary', [
				'booking' => $booking,
			]);
		if ($booking->state === BookingState::BILLING)
			return redirect()->route('booking.billing', [
				'booking' => $booking,
			]);
		if ($booking->state === BookingState::MEALS)
			return redirect()->route('booking.meals', [
				'booking' => $booking,
			]);
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

	public function availableCheckouts(Request $request, Booking $booking, Room $room): JsonResponse
	{
		$validated = $request->validate([
			'checkin' => 'required|date',
		]);

		//$booking = Booking::findOrFail($request->session()->get('booking'));
		$this->assertBookingState($request, $booking, BookingState::ROOMS);
		$booking->save();

		return response()->json([
			'checkouts' => $room->availableCheckouts($validated['checkin']),
			'sessionExpireSeconds' => floor(now()->diffInSeconds($booking->expires_at)),
		]);
	}

	public function maxPeople(Request $request, Booking $booking, Room $room): JsonResponse
	{
		$validated = $request->validate([
			'checkin' => 'required|date',
			'checkout' => 'required|date|after:checkin',
		]);

		//$booking = Booking::findOrFail($request->session()->get('booking'));
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
		@set_time_limit(55);

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
			throw ValidationException::withMessages(['buy_option' => __('Invalid buy option.')]);
		$multiBookable = $buyOption['people'] === 0;
		if ($multiBookable) {
			if ($validated['people'] === null)
				throw ValidationException::withMessages(['people' => __('This room requires to specify a number of people.')]);
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

			DB::transaction(static function() use ($reservation, $toSave): void {
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

		//$request->session()->put('editedRooms', true);

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

		@set_time_limit(55);

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

		DB::transaction(static function() use ($reservation, $toSave, $toDelete): void {
			foreach ($toSave as $r)
				$r->save();

			foreach ($toDelete as $r)
				$r->delete();
			$reservation->delete();
		});

		//$request->session()->put('editedRooms', true);

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

		//if ($request->session()->get('editedRooms', false)) {
			@set_time_limit(55);
			// $request->session()->put('editedRooms', false);
			$this->addMissingMeals($booking);
		// }

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

		$meals = $booking->meals()->with('meal')->whereRelation('meal', 'type', $type)->where('date', $date->format('Y-m-d'))->get();
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
				$meal->loadMissing('meal');
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
			else $toSave[] = $meal;
		}

		DB::transaction(static function() use ($toSave, $toDelete): void {
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

		$countries = Country::all();

		return inertia('Booking/Billing', [
			'booking' => $booking,
			'hotels' => $hotels,
			'countries' => $countries,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function addDiscount(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::BILLING);

		if (!$request->user())
			return redirect()->back()->with([
				'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
				'flash' => [
					'message' => __('You are not an admin.'),
					'location' => 'toast-tc',
					'timeout' => 5000,
					'style' => 'error',
				],
			]);

		$validated = $request->validate([
			'discount' => 'required|decimal:0,2|min:0',
		]);

		$booking->discount = $validated['discount'];
		$booking->save();

		return redirect()->back()->with([
			'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
			'flash' => [
				'message' => __('Discount successfully added.'),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'success',
			],
		]);
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

		if ($booking->has('billingInfo'))
			$booking->billingInfo()->update($validated);
		else $booking->billingInfo()->create([
			'email' => $booking->email,
			...$validated,
		]);

		$booking->billingInfo()->updateOrCreate([], [
			'email' => $booking->email,
			...$validated,
		]);

		$booking->state = BookingState::SUMMARY;
		$booking->save();

		return redirect()->route('booking.summary', [
			'booking' => $booking,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function summary(Request $request, Booking $booking): Response
	{
		$this->assertBookingState($request, $booking, [BookingState::SUMMARY, BookingState::PAYMENT]);

		$booking->state = BookingState::SUMMARY;
		$booking->save();

		$booking->loadMissing('rooms', 'rooms.room', 'meals', 'meals.meal', 'billingInfo');
		$hotels = Hotel::all();

		$sandbox = config('paypal.mode') === 'sandbox';
		$clientId = $sandbox ? config('paypal.sandbox.client_id') : config('paypal.live.client_id');

		return inertia('Booking/Summary', [
			'booking' => $booking,
			'hotels' => $hotels,
			'sandbox' => $sandbox,
			'pp_client_id' => $clientId,
		])->with('sessionExpireSeconds', floor(now()->diffInSeconds($booking->expires_at)));
	}

	public function createOrder(Request $request, Booking $booking): JsonResponse
	{
		$this->assertBookingState($request, $booking, BookingState::SUMMARY);

		$booking->state = BookingState::PAYMENT;
		$booking->save();

		$provider = new PayPal();
		$provider->setApiCredentials(config('paypal'));
		$provider->getAccessToken();

		$roomsPrice = DB::table('room_reservations')->where('booking_id', $booking->id)->select(DB::raw('SUM(price) AS price'))->first()->price;
		$mealsTotals = DB::table('meal_reservations')->where('booking_id', $booking->id)->select(DB::raw('SUM(price) AS price'), DB::raw('SUM(discount) as discount'))->first();
		$mealsPrice = floatval($mealsTotals->price) - floatval($mealsTotals->discount);
		$totalPrice = floatval($roomsPrice) + $mealsPrice - floatval($booking->discount);

		$options = [
			'intent' => 'CAPTURE',
			'payment_source' => [
				'paypal' => [
					'experience_context' => [
						'user_action' => 'PAY_NOW',
						'locale' => app()->isLocale('it') ? 'it-IT' : 'en-US',
						'shipping_preference' => 'NO_SHIPPING',
						'return_url' => route('booking.success', [
							'booking' => $booking,
						]),
						'cancel_url' => route('booking.abort', [
							'booking' => $booking,
						]),
					],
				],
			],
			'purchase_units' => [
				0 => [
					'custom_id' => $booking->id,
					'invoice_id' => 'GARFALUDICA-' . mb_str_pad((string)($booking->short_id), 4, '0', \STR_PAD_LEFT),
					'soft_descriptor' => 'GOBCON24',
					'amount' => [
						'currency_code' => 'EUR',
						'value' => number_format($totalPrice, 2, '.', ''),
					],
				],
			],
		];

		$response = $provider->createOrder($options);

		if (isset($response['id']) && $response['id'] != null) {
			// foreach ($response['links'] as $link)
			// 	if ($link['rel'] === 'approve')
			// 		return response()->json([
			// 			'success' => true,
			// 			'approveUrl' => $link['href'],
			// 		]);
			return response()->json([
				'success' => true,
				'orderId' => $response['id'],
			]);
		}

		$booking->state = BookingState::SUMMARY;
		$booking->save();

		return response()->json([
			'success' => false,
			'error' => __('An error has occured. Please, try again.'),
		]);
	}

	public function captureOrder(Request $request, Booking $booking, string $orderId): JsonResponse
	{
		$this->assertBookingState($request, $booking, BookingState::PAYMENT);

		$provider = new PayPal();
		$provider->setApiCredentials(config('paypal'));
		$provider->getAccessToken();

		$response = $provider->capturePaymentOrder($orderId);

		if (isset($response['status'])) {
			switch ($response['status']) {
				case 'COMPLETED':
					$booking->state = BookingState::COMPLETED;
					$booking->pp_order_id = $orderId;
					$booking->save();
					dispatch(function() use ($booking, $orderId, $response): void {
						$this->orderCompleted($booking, $orderId, $response);
					});
					return response()->json([
						'success' => true,
					]);
				case 'PAYER_ACTION_REQUIRED':
					$continueUrl = null;

					foreach ($response['links'] as $link) {
						if ($link['rel'] === 'payer-action')
							$continueUrl = $link['href'];
					}
					return response()->json([
						'success' => false,
						'recoverable' => true,
						'continueUrl' => $continueUrl,
					]);
				default:
					$booking->state = BookingState::FAILED;
					$booking->save();
					Log::error('PayPal capture order status: ' . $response['status'] . ' (id: ' . $response['id'] + '; booking: ' . $booking->id . ').');
					return response()->json([
						'success' => false,
						'recoverable' => false,
						'error' => __('An unrecoverable error has occured. Order cancelled.'),
					]);
			}
		}

		if (isset($response['error'], $response['error']['details'], $response['error']['details'][0])) {
			$issue = $response['error']['details'][0]['issue'];
			if ($issue === 'INSTRUMENT_DECLINED')
				return response()->json([
					'success' => false,
					'recoverable' => true,
				]);
			if ($issue === 'DUPLICATE_INVOICE_ID')
				return response()->json([
					'success' => false,
					'recoverable' => false,
					'error' => __('Cannot proceed. Seems like you already made the payment, but we didn\'t collected it properly. Please, contact info@garfaludica.it to get this solved.'),
				]);
		}

		$booking->state = BookingState::FAILED;
		$booking->save();

		return response()->json([
			'success' => false,
			'recoverable' => false,
			'error' => __('An unrecoverable error has occured. Order cancelled.'),
		]);
	}

	public function confirmBooking(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, BookingState::SUMMARY);
		if (!$request->user())
			return redirect()->back()->with([
				'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
				'flash' => [
					'message' => __('You are not an admin.'),
					'location' => 'toast-tc',
					'timeout' => 5000,
					'style' => 'error',
				],
			]);

		$roomsPrice = DB::table('room_reservations')->where('booking_id', $booking->id)->select(DB::raw('SUM(price) AS price'))->first()->price;
		$mealsTotals = DB::table('meal_reservations')->where('booking_id', $booking->id)->select(DB::raw('SUM(price) AS price'), DB::raw('SUM(discount) as discount'))->first();
		$mealsPrice = floatval($mealsTotals->price) - floatval($mealsTotals->discount);
		$totalPrice = floatval($roomsPrice) + $mealsPrice - floatval($booking->discount);

		if ($totalPrice < -0.01 || $totalPrice > 0.01 || floatval($booking->discount) < 0.01)
			return redirect()->back()->with([
				'sessionExpireSecionds' => floor(now()->diffInSeconds($booking->expires_at)),
				'flash' => [
					'message' => __('An error has occured. Please, try again.'),
					'location' => 'toast-tc',
					'timeout' => 5000,
					'style' => 'error',
				],
			]);

		$booking->state = BookingState::COMPLETED;
		$booking->save();
		dispatch(function() use ($booking): void {
			$this->orderCompleted($booking);
		});
		return redirect()->route('booking.success', [
			'booking' => $booking,
		]);
	}

	public function successOrder(Request $request, Booking $booking): Response
	{
		if ($booking->state !== BookingState::COMPLETED)
			abort(404);

		return inertia('Booking/Success', [
			'booking' => $booking,
		]);
	}

	public function abortOrder(Request $request, Booking $booking): Response
	{
		if (!\in_array($booking->state, [BookingState::CANCELLED, BookingState::PAYMENT, BookingState::FAILED], true))
			abort(404);

		if ($booking->state === BookingState::PAYMENT) {
			$booking->state = BookingState::CANCELLED;
			$booking->save();
		}

		return inertia('Booking/Abort', [
			'booking' => $booking,
		]);
	}

	public function terminate(Request $request, Booking $booking): RedirectResponse
	{
		// if (!$request->session()->has('booking')
			// || $request->session()->get('booking') !== $booking->id)
				// return redirect()->route(app()->isLocale('it') ? 'home' : 'en.home');
		$booking->delete();
		// $request->session()->invalidate();
		return redirect()->route(app()->isLocale('it') ? 'home' : 'en.home')->with('flash', [
			'message' => __('Sorry, your session has expired.'),
			'location' => 'modal',
			'style' => 'info',
		]);
	}

	public function resetOrder(Request $request, Booking $booking): RedirectResponse
	{
		$this->assertBookingState($request, $booking, [BookingState::ROOMS, BookingState::MEALS, BookingState::BILLING]);
		DB::transaction(static function() use ($booking): void {
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

	public function manageBooking(Request $request, Booking $booking): Response
	{
		if (!$request->hasValidSignature()
			|| !in_array($booking->state, [BookingState::COMPLETED, BookingState::REFUND_REQUESTED, BookingState::REFUNDED])
			|| Carbon::parse('2024-06-16', 'UTC')->startOfDay()->isPast())
			abort(404);

		if (in_array($booking->state, [BookingState::REFUND_REQUESTED, BookingState::REFUNDED]))
			return inertia('Booking/Refund', [
				'refunded' => $booking->state === BookingState::REFUNDED,
			]);

		$booking->loadMissing('rooms', 'rooms.room', 'meals', 'meals.meal', 'billingInfo');
		$hotels = Hotel::all();

		$refundable = true;
		foreach ($booking->rooms as $room) {
			$time = $room->room->checkin_time;
			$checkin = $room->checkin->copy()->setTimeFrom($time);
			if (now()->diffInHours($checkin) < 24) {
				$refundable = false;
				break;
			}
		}

		$countries = Country::all();

		return inertia('Booking/Manage', [
			'booking' => $booking,
			'hotels' => $hotels,
			'refundable' => $refundable,
			'countries' => $countries,
		]);
	}

	public function addNotes(Request $request, Booking $booking): RedirectResponse
	{
		if ($booking->state !== BookingState::COMPLETED)
			abort(404);

		$validated = $request->validate([
			'notes' => 'required|string|min:1|max:4096',
		]);

		$newNotes = trim($validated['notes']);
		$datetime = now()->setTimezone('Europe/Rome')->format('Y-m-d H:i:s');
		$booking->notes .= "\n\n*** UPDATE {$datetime} ***\n" . $newNotes;
		$booking->save();

		return redirect()->back()->with([
			'flash' => [
				'message' => __('Notes successfully updated.'),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'success',
			],
		]);
	}

	public function updateBilling(Request $request, Booking $booking): RedirectResponse
	{
		if ($booking->state !== BookingState::COMPLETED)
			abort(404);

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

		$booking->billingInfo()->update($validated);

		return redirect()->back()->with([
			'flash' => [
				'message' => __('Billing informations successfully updated.'),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'success',
			],
		]);
	}

	public function refundBooking(Request $request, Booking $booking): RedirectResponse
	{
		if ($booking->state !== BookingState::COMPLETED)
			abort(404);

		$booking->state = BookingState::REFUND_REQUESTED;
		$booking->save();

		return redirect()->back();
	}

	protected function assertBookingState(Request $request, Booking $booking, array|BookingState $state): void
	{
		// if (!$request->session()->has('booking')
			// || $request->session()->get('booking') !== $booking->id)
				// abort(404);
		if ($booking->expires_at->isPast()) {
			// $request->session()->invalidate();
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

	private function orderCompleted(Booking $booking, ?string $orderId = null, ?array $response = null): void
	{
		if ($orderId)
			Cache::rememberForever('pp_order_' . $orderId, static fn() => $response);
		else
			$orderId = 'GARFALUDICA-ADMIN-ORDER';

		$imageContent = Storage::get('garfaludica.jpg');
		$logoUrl = 'data:image/jpeg;base64,' . base64_encode($imageContent);

		$orderIdLabel = __('Order ID');
		$receiptNumLabel = __('Receipt #');
		$dateLabel = __('Date');
		$codeLabel = __('Code');
		$itemHeadLabel = __('Room/Meal');
		$priceHeadLabel = __('Price');
		$discountLabel = __('Discount');
		$totalLabel = __('Total');
		$receiptNote = __('Pro-forma, non-fiscal receipt');

		$receiptNum = 'GARFALUDICA-' . mb_str_pad((string)($booking->short_id), 4, '0', \STR_PAD_LEFT);
		$date = now()->setTimezone('Europe/Rome')->format('Y-m-d H:i:s');
		$code = $booking->id;

		$firstName = htmlspecialchars($booking->billingInfo->first_name);
		$lastName = htmlspecialchars($booking->billingInfo->last_name);
		$taxId = htmlspecialchars($booking->billingInfo->tax_id);
		$address = htmlspecialchars($booking->billingInfo->address_line_1) . ($booking->billingInfo->address_line_2 ? '<br />' . htmlspecialchars($booking->billingInfo->address_line_2) : '');
		$city = htmlspecialchars($booking->billingInfo->city);
		$state = htmlspecialchars($booking->billingInfo->state);
		$postalCode = htmlspecialchars($booking->billingInfo->postal_code);
		$countryCode = htmlspecialchars(strtoupper($booking->billingInfo->country_code));
		$email = htmlspecialchars($booking->billingInfo->email);
		$phone = htmlspecialchars($booking->billingInfo->phone);

		$locale = app()->isLocale('it') ? 'it' : 'en';
		$total = 0.0;
		$discount = 0.0;
		$items = [];
		$rooms = $booking->rooms()->with('room', 'room.hotel')->get();

		foreach ($rooms as $room) {
			$desc = $room->room->name[$locale];
			if ($room->buy_option[$locale] !== 'default')
				$desc .= ' (' . $room->buy_option[$locale] . ')';
			$desc .= ' - ' . __('hotel_name_' . $room->room->hotel->name);
			$desc .= ' [' . $room->checkin->format('d/m') . ' - ' . $room->checkout->format('d/m') . ']';
			$items[] = [
				'label' => htmlspecialchars($desc),
				'price' => '€ ' . number_format((float)($room->price), 2, '.', ''),
			];
			$total += $room->price;
		}
		$meals = $booking->meals()->with('meal', 'meal.hotel')->get();

		foreach ($meals as $meal) {
			$desc = $meal->quantity . 'x ';
			$desc .= __($meal->meal->type->value);
			if ($meal->meal->menu !== Menu::STANDARD)
				$desc .= ' (' . __($meal->meal->menu->value) . ')';
			$desc .= ' - ' . __('hotel_name_' . $meal->meal->hotel->name);
			$desc .= ' [' . $meal->date->format('d/m') . ']';
			$items[] = [
				'label' => htmlspecialchars($desc),
				'price' => '€ ' . number_format((float)($meal->price), 2, '.', ''),
			];
			$total += $meal->price;
			$discount += $meal->discount;
		}

		$total -= $discount;
		$discount = '€ ' . number_format(-$discount, 2, '.', '');
		$total = '€ ' . number_format($total, 2, '.', '');

		$options = new Options();
		// $options->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($options);

		$htmlTemplate = <<<EOD

					<html>
						<head>
							<style>
								.invoice-box {
									max-width: 800px;
									margin: auto;
									padding: 30px;
									border: 1px solid #eee;
									box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
									font-size: 14px;
									line-height: 20px;
									font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
									color: #555;
								}

								.invoice-box table {
									width: 100%;
									line-height: inherit;
									text-align: left;
								}

								.invoice-box table td {
									padding: 5px;
									vertical-align: top;
								}

								.invoice-box table tr td:nth-child(2) {
									text-align: right;
								}

								.invoice-box table tr.top table td {
									padding-bottom: 20px;
								}

								.invoice-box table tr.top table td.title {
									font-size: 20px;
									line-height: 30px;
									color: #333;
								}

								.invoice-box table tr.information table td {
									padding-bottom: 40px;
								}

								.invoice-box table tr.heading td {
									background: #eee;
									border-bottom: 1px solid #ddd;
									font-weight: bold;
								}

								.invoice-box table tr.details td {
									padding-bottom: 20px;
								}

								.invoice-box table tr.item td {
									border-bottom: 1px solid #eee;
								}

								.invoice-box table tr.item.last td {
									border-bottom: none;
								}

								.invoice-box table tr.total td:nth-child(2) {
									border-top: 2px solid #eee;
									font-weight: bold;
								}

								@media only screen and (max-width: 600px) {
									.invoice-box table tr.top table td {
										width: 100%;
										display: block;
										text-align: center;
									}

									.invoice-box table tr.information table td {
										width: 100%;
										display: block;
										text-align: center;
									}
								}

								/** RTL **/
								.invoice-box.rtl {
									direction: rtl;
									font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
								}

								.invoice-box.rtl table {
									text-align: right;
								}

								.invoice-box.rtl table tr td:nth-child(2) {
									text-align: left;
								}
							</style>
						</head>

						<body>
							<div class="invoice-box">
								<table cellpadding="0" cellspacing="0">
									<tr class="top">
										<td colspan="2">
											<table>
												<tr>
													<td class="title">
														<img src="$logoUrl" style="width: 100%; max-width: 150px" />
													</td>
													<td>
														{$receiptNumLabel}: {$receiptNum}<br />
														{$dateLabel}: {$date}<br />
														{$orderIdLabel}: {$orderId}<br />
														{$codeLabel}: {$code}<br />
														{$receiptNote}
													</td>
												</tr>
											</table>
										</td>
									</tr>

									<tr class="information">
										<td colspan="2">
											<table>
												<tr>
													<td>
														Garfaludica APS<br />
														Ente del Terzo Settore (RUNTS 113019)<br />
														Tana dei Goblin di Castelnuovo di Garfagnana<br />
														C.F.: 90011570463<br />
														Località Braccicorti, 38/A - 55036 Pieve Fosciana (LU)<br />
														info@garfaludica.it - garfaludica@pec.it<br />
														https://www.garfaludica.it
													</td>
													<td>
														{$firstName} {$lastName}<br />
														{$taxId}<br />
														{$address}<br />
														{$city}, {$state} {$postalCode}<br />
														{$countryCode}<br />
														{$email}<br />
														{$phone}
													</td>
												</tr>
											</table>
										</td>
									</tr>

									<tr class="heading">
										<td>{$itemHeadLabel}</td>
										<td>{$priceHeadLabel}</td>
									</tr>

			EOD;

		foreach ($items as $item)
			$htmlTemplate .= <<<EOD

										<tr class="item">
											<td>{$item['label']}</td>
											<td>{$item['price']}</td>
										</tr>

				EOD;

		$htmlTemplate .= <<<EOD

									<tr class="item last">
										<td>{$discountLabel}</td>
										<td>{$discount}</td>
									</tr>

									<tr class="total">
										<td></td>
										<td>{$totalLabel}: {$total}</td>
									</tr>
								</table>
							</div>
						</body>
					</html>

			EOD;

		$dompdf->loadHtml($htmlTemplate);
		$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();

		Storage::put('receipts/' . $booking->id . '.pdf', $dompdf->output());
		$receiptPath = storage_path('app/receipts/' . $booking->id . '.pdf');

		Mail::to($booking->email)->queue(new OrderReceipt($booking, $receiptPath));
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

		DB::transaction(static function() use ($toSave): void {
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
}

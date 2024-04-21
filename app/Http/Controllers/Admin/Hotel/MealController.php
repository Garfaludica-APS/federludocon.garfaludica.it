<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Meal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class MealController extends Controller
{
	use AuthorizesRequests;

	/**
	 * Display a listing of the resource.
	 */
	public function index(Hotel $hotel): Response
	{
		$this->authorize('viewAny', [Meal::class, $hotel]);
		$admin = auth()->user();
		return inertia('Admin/Hotel/Meals/Index', [
			'hotel' => $hotel,
			'meals' => $hotel->meals()->withTrashed()->get()->transform(static fn($meal) => [
				'id' => $meal->id,
				'type' => $meal->type,
				'menu' => $meal->menu,
				'price' => $meal->price,
				'meal_time' => $meal->meal_time,
				'reservable' => $meal->reservable,
				'created_at' => $meal->created_at,
				'updated_at' => $meal->updated_at,
				'deleted_at' => $meal->deleted_at,
				'can' => [
					'update' => $admin->can('update', [Meal::class, $hotel, $meal]),
					'delete' => $admin->can('delete', [Meal::class, $hotel, $meal]),
					'restore' => $admin->can('restore', [Meal::class, $hotel, $meal]),
				],
			]),
			'canCreateMeals' => $admin->can('create', [Meal::class, $hotel]),
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(Hotel $hotel): Response
	{
		$this->authorize('create', [Meal::class, $hotel]);
		return inertia('Admin/Hotel/Meals/Create', [
			'hotel' => $hotel,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, Hotel $hotel): RedirectResponse
	{
		$this->authorize('create', [Meal::class, $hotel]);

		$validated = $request->validate([
			'type' => 'required|string|in:breakfast,lunch,dinner',
			'menu' => 'required|string|in:standard,vegetarian,vegan',
			'price' => 'required|decimal:0,2|min:0',
			'meal_time' => 'required|date_format:H:i',
			'reservable' => 'required|boolean',
		]);

		$hotel->meals->create($validated);

		return redirect()->route('admin.hotels.meals.index', $hotel)->with('flash', [
			'message' => __('Meal successfully created.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Hotel $hotel, Meal $meal): Response
	{
		$this->authorize('update', [Meal::class, $hotel, $meal]);
		$admin = auth()->user();
		return inertia('Admin/Hotel/Meals/Edit', [
			'meal' => [
				'id' => $meal->id,
				'hotel' => $hotel,
				'type' => $meal->type,
				'menu' => $meal->menu,
				'price' => $meal->price,
				'meal_time' => $meal->meal_time,
				'reservable' => $meal->reservable,
				'created_at' => $meal->created_at,
				'updated_at' => $meal->updated_at,
				'deleted_at' => $meal->deleted_at,
				'can' => [
					'delete' => $admin->can('delete', [Meal::class, $hotel, $meal]),
					'restore' => $admin->can('restore', [Meal::class, $hotel, $meal]),
				],
			],
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Hotel $hotel, Meal $meal): RedirectResponse
	{
		$this->authorize('update', [Meal::class, $hotel, $meal]);

		$validated = $request->validate([
			'type' => 'required|string|in:breakfast,lunch,dinner',
			'menu' => 'required|string|in:standard,vegetarian,vegan',
			'price' => 'required|decimal:0,2|min:0',
			'meal_time' => 'required|date_format:H:i',
			'reservable' => 'required|boolean',
		]);

		$meal->update($validated);

		return redirect()->route('admin.hotels.meals.index', $hotel)->with('flash', [
			'message' => __('Meal successfully updated.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Hotel $hotel, Meal $meal): RedirectResponse
	{
		$this->authorize('delete', [Meal::class, $hotel, $meal]);

		if ($meal->trashed())
			return redirect()->route('admin.hotels.meals.index', $hotel)->with('flash', [
				'message' => __('Can not delete meal: meal already deleted.'),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'error',
			]);

		$meal->delete();

		return redirect()->route('admin.hotels.meals.index', $hotel)->with('flash', [
			'message' => __('Meal successfully deleted.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}

	public function restore(Hotel $hotel, Meal $meal): RedirectResponse
	{
		$this->authorize('restore', [Meal::class, $hotel, $meal]);

		if (!$meal->trashed())
			return redirect()->route('admin.hotels.meals.index', $hotel)->with('flash', [
				'message' => __('Can not restore meal: meal already active.'),
				'location' => 'toast-tc',
				'timeout' => 5000,
				'style' => 'error',
			]);

		$meal->restore();

		return redirect()->route('admin.hotels.meals.index', $hotel)->with('flash', [
			'message' => __('Meal successfully restored.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}
}

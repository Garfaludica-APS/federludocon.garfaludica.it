<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin\Hotel;

use App\Http\Controllers\Controller;
use App\Lang\Manager;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Response;

class PresentationController extends Controller
{
	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Manager $manager, Hotel $hotel): Response
	{
		if (Gate::denies('update-hotel-presentation', $hotel))
			abort(403);

		$manager->importTranslations();
		$key = 'hotel_' . Str::transliterate($hotel->name) . '_presentation_text';
		$locales = $manager->getLocales();
		$locale = App::getLocale();
		$idx = array_search($locale, $locales);
		if ($idx === false)
			$idx = 0;
		return inertia('Admin/Hotel/Presentation/Edit', [
			'hotel' => $hotel,
			'locales' => $manager->getLocales(),
			'presentation' => $manager->getTranslationsForKey($key),
			'startTabIndex' => $idx,
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Manager $manager, Hotel $hotel): RedirectResponse
	{
		if (Gate::denies('update-hotel-presentation', $hotel))
			abort(403);

		$validated = $request->validate([
			'presentation' => 'required|array',
			'presentation.*' => 'required|string',
		]);
		$manager->importTranslations();

		foreach ($validated['presentation'] as $locale => $text) {
			$key = 'hotel_' . Str::transliterate($hotel->name) . '_presentation_text';
			$manager->translate($key, $locale, $text);
		}
		$manager->exportTranslations();
		return redirect()->route('admin.hotel.presentation.edit', $hotel)->with('flash', [
			'message' => __('Presentation page updated successfully.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}
}

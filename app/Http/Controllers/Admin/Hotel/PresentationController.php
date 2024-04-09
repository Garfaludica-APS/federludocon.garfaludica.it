<?php

namespace App\Http\Controllers\Admin\Hotel;

use App\Http\Controllers\Controller;
use App\Lang\Manager;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Response;

class PresentationController extends Controller
{
	/**
	 * Show the form for editing the specified resource.
	 */
	public function show(Manager $manager, Hotel $hotel): Response
	{
		$manager->importTranslations();
		$key = 'hotel_' + Str::transliterate($hotel->name) + '_presentation_text';
		return inertia('Admin/Hotel/Presentation/Edit', [
			'hotel' => $hotel,
			'presentation' => $manager->getTranslationsForKey($key),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Manager $manager, Hotel $hotel): RedirectResponse
	{
		$manager->importTranslations();
		$manager->exportTranslations();
		return redirect()->route('admin.hotels.presentation.show', $hotel)->with('flash', [
			'message' => __('Presentation page updated successfully.'),
			'location' => 'toast-tc',
			'timeout' => 5000,
			'style' => 'success',
		]);
	}
}

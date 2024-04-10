<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Inertia\Response;

class HotelController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(): Response
	{
		return inertia('Hotels', [
			'hotels' => Hotel::all(),
		]);
	}
}

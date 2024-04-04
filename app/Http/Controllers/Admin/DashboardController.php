<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;

class DashboardController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request): Response
	{
		return inertia('Admin/Dashboard');
	}
}

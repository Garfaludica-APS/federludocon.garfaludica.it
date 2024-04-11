<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

use Illuminate\Support\Carbon;

return [
	'open' => \is_string(env('GOBCON_BOOKING_OPEN', true))
		? Carbon::parse(env('GOBCON_BOOKING_OPEN'))
		: env('GOBCON_BOOKING_OPEN', true),
];

<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

use Illuminate\Support\Carbon;

return [
	'open' => \is_string(env('FEDERLUDOCON_BOOKING_OPEN', true))
		? Carbon::parse(env('FEDERLUDOCON_BOOKING_OPEN'))
		: env('FEDERLUDOCON_BOOKING_OPEN', true),
	'close' => \is_string(env('FEDERLUDOCON_BOOKING_CLOSE', false))
		? Carbon::parse(env('FEDERLUDOCON_BOOKING_CLOSE'))
		: env('FEDERLUDOCON_BOOKING_CLOSE', false),
];

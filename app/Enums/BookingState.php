<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Enums;

enum BookingState: string
{
	case START = 'start';
	case ROOMS = 'rooms';
	case MEALS = 'meals';
	case BILLING = 'billing';
	case SUMMARY = 'summary';
	case PAYMENT = 'payment';
	case COMPLETED = 'completed';
	case FAILED = 'failed';
	case CANCELLED = 'cancelled';
	case REFUNDED = 'refunded';
}

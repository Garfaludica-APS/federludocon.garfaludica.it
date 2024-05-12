<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Enums;

enum Menu: string
{
	case STANDARD = 'standard';
	case VEGETARIAN = 'vegetarian';
	case VEGAN = 'vegan';
}

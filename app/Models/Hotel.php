<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
	];

	public function admins(): BelongsToMany
	{
		return $this->belongsToMany(Admin::class);
	}
}

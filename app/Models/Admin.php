<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
	use HasFactory;
	use Notifiable;
	protected $fillable = [
		'username',
		'email',
		'password',
	];
	protected $hidden = [
		'password',
		'remember_token',
	];

	public function invites(): HasMany
	{
		return $this->hasMany(Invite::class, 'created_by');
	}

	protected function casts(): array
	{
		return [
			'password' => 'hashed',
		];
	}
}

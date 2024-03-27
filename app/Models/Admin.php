<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
	use HasFactory;
	use Notifiable;
	use CanResetPassword;

	protected $fillable = [
		'username',
		'email',
		'password',
		'is_super_admin',
	];
	protected $hidden = [
		'password',
		'remember_token',
	];

	public function createdInvitations(): HasMany
	{
		return $this->hasMany(Invitation::class, 'created_by');
	}

	public function invitedAdmins(): HasManyThrough
	{
		return $this->hasManyThrough(
			Admin::class,
			Invitation::class,
			'created_by',
			'invitation_id',
		);
	}

	protected function casts(): array
	{
		return [
			'password' => 'hashed',
			'is_super_admin' => 'boolean',
			'accepted_at' => 'datetime',
		];
	}
}

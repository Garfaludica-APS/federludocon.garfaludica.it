<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
	use CanResetPassword;
	use HasFactory;
	use Notifiable;

	protected $fillable = [
		'username',
		'email',
		'password',
		'is_super_admin',
		'hotels',
	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	public static function boot(): void
	{
		parent::boot();
		static::deleting(static function($admin): void {
			$invitation = $admin->invitation();
			if ($invitation)
				$invitation->delete();
		});
	}

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

	public function inviter(): HasOneThrough
	{
		return $this->hasOneThrough(
			Admin::class,
			Invitation::class,
			'id',
			'id',
			'invitation_id',
			'created_by',
		);
	}

	public function invitation(): BelongsTo
	{
		return $this->belongsTo(Invitation::class, 'invitation_id');
	}

	public function hotels(): BelongsToMany
	{
		return $this->belongsToMany(Hotel::class);
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

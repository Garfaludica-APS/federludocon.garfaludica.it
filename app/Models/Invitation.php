<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invitation extends Model
{
	use HasFactory;
	use HasUlids;

	public const UPDATED_AT = null;

	protected $fillable = [
		'email',
		'token',
		'is_super_admin',
	];

	protected $hidden = [
		'token',
	];

	public function creator(): BelongsTo
	{
		return $this->belongsTo(Admin::class, 'created_by');
	}

	public function invitee(): HasOne
	{
		return $this->hasOne(Admin::class, 'invitation_id');
	}

	protected function casts(): array
	{
		return [
			'is_super_admin' => 'boolean',
		];
	}

	protected function email(): Attribute
	{
		return Attribute::make(
			set: fn (string $value) => strtolower($value),
		);
	}
}

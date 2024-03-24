<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invite extends Model
{
	use HasFactory;
	use HasUlids;
	public const UPDATED_AT = null;
	protected $fillable = [
		'email',
		'created_by',
		'token',
	];
	protected $hidden = [
		'token',
	];

	public function admin(): BelongsTo
	{
		return $this->belongsTo(Admin::class, 'created_by');
	}
}

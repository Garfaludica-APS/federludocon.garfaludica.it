<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('invitations', static function(Blueprint $table): void {
			$table->ulid('id')->primary();
			$table->string('email', length: 254)->unique();
			$table->string('token');
			$table->boolean('is_super_admin')->default(false);
			$table->foreignId('created_by')->nullable()->constrained(
				table: 'admins',
			)->cascadeOnUpdate()->nullOnDelete();
			$table->timestamp('created_at')->useCurrent();
			$table->timestamp('accepted_at')->nullable()->default(null);
		});

		Schema::table('admins', static function(Blueprint $table): void {
			$table->foreignUlid('invitation_id')
				->nullable()
				->constrained()
				->cascadeOnUpdate()->nullOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropColumns('admins', 'invitation_id');
		Schema::dropIfExists('invitations');
	}
};

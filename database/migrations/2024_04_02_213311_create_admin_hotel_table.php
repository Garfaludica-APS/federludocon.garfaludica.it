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
		Schema::create('admin_hotel', static function(Blueprint $table): void {
			$table->foreignId('admin_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
			$table->foreignId('hotel_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
			$table->primary(['admin_id', 'hotel_id']);
		});

		Schema::table('invitations', static function(Blueprint $table): void {
			$table->json('hotels')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('admin_hotel');
	}
};

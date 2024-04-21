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
		Schema::create('bookings', static function(Blueprint $table): void {
			$table->uuid('id')->primary();
			$table->unsignedSmallInteger('short_id')->unique();
			$table->string('email', length: 254);
			$table->enum('state', ['start', 'rooms', 'meals', 'billing', 'summary', 'payment', 'completed', 'failed', 'cancelled', 'refunded'])->default('start');
			$table->text('notes')->nullable();
			$table->string('pp_order_id', 36)->nullable();
			$table->timestamps();
		});
		Schema::table('bookings', static function(Blueprint $table): void {
			$table->increments('short_id')->change();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('bookings');
	}
};

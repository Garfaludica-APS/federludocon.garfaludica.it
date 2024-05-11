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
		Schema::create('room_reservations', static function(Blueprint $table): void {
			$table->id();
			$table->foreignUuid('booking_id')->constrained(
				column: 'id',
			)->cascadeOnUpdate()->cascadeOnDelete();
			$table->unsignedInteger('buy_option_id');
			$table->foreignId('room_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
			$table->unsignedTinyInteger('people');
			$table->dateTime('checkin');
			$table->dateTime('checkout');
			$table->decimal('price');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('room_reservations');
	}
};

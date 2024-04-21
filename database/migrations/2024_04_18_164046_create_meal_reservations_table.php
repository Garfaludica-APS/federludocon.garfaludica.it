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
		Schema::create('meal_reservations', static function(Blueprint $table): void {
			$table->id();
			$table->foreignUuid('booking_id')->constrained(
				column: 'id',
			)->cascadeOnUpdate()->restrictOnDelete();
			$table->foreignId('meal_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
			$table->date('date');
			$table->decimal('price');
			$table->unsignedInteger('quantity')->default(1);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('meal_reservations');
	}
};

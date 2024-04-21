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
		Schema::create('meals', static function(Blueprint $table): void {
			$table->id();
			$table->foreignId('hotel_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
			$table->enum('type', ['breakfast', 'lunch', 'dinner']);
			$table->enum('menu', ['standard', 'vegetarian', 'vegan']);
			$table->decimal('price');
			$table->time('meal_time');
			$table->boolean('reservable')->default(true);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('meals');
	}
};

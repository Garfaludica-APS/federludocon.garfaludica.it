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
		Schema::create('rooms', static function(Blueprint $table): void {
			$table->id();
			$table->foreignId('hotel_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
			$table->json('name');
			$table->json('description')->nullable();
			$table->integer('quantity');
			$table->json('buy_options');
			$table->time('checkin_time');
			$table->time('checkout_time');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('rooms');
	}
};

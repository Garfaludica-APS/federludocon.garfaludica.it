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
		Schema::create('billing_infos', static function(Blueprint $table): void {
			$table->id();
			$table->foreignUuid('booking_id')->constrained(
				column: 'id',
			)->cascadeOnUpdate()->cascadeOnDelete();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('tax_id', 20);
			$table->string('address_line_1', 300);
			$table->string('address_line_2', 300)->nullable();
			$table->string('city', 120);
			$table->string('state', 300);
			$table->string('postal_code', 60);
			$table->char('country_code', 2)->default('IT');
			$table->string('email', 254);
			$table->string('phone', 15)->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('billing_infos');
	}
};

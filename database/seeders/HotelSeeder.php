<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		Hotel::create(['name' => 'Panoramic Hotel']);
		Hotel::create(['name' => 'Isera Refuge']);
		Hotel::create(['name' => 'Braccicorti Farmhouse']);
	}
}

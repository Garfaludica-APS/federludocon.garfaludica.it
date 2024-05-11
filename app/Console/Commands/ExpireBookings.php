<?php

namespace App\Console\Commands;

use App\Enums\BookingState;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;

class ExpireBookings extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bookings:expire';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete expired bookings.';

	/**
	 * Execute the console command.
	 */
	public function handle(): void
	{
		$lock = Cache::lock('bookings', 5);
		try {
			$lock->block(20);
			Booking::where('expires_at', '<', now())->whereNotIn('state', [BookingState::PAYMENT, BookingState::COMPLETED, BookingState::FAILED, BookingState::CANCELLED, BookingState::REFUNDED])->delete();
		} catch (LockTimeoutException $e) {
			return;
		} finally {
			$lock?->release();
		}
	}
}

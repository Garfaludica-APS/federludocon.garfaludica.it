<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class SendPasswordResetLink extends Command implements PromptsForMissingInput
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:send-reset-link
				{admin : The ID of the admin}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send the password reset link to the specified admin';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		// TODO: send mail
		$this->info('Password reset mailed to the admin.');
	}

	protected function promptForMissingArgumentsUsing(): array
	{
		return [
			'admin' => static fn() => search(
				label: 'Search for an admin:',
				placeholder: 'E.g. admin',
				options: static fn($value) => \mb_strlen($value) > 0
					? Admin::where('username', 'like', "%{$value}%")->pluck('username', 'id')->all()
					: [],
			),
		];
	}
}

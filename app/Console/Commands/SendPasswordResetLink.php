<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

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
	protected $signature = <<<'EOD'
		app:send-reset-link
						{admin : The ID of the admin}
		EOD;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send the password reset link to the specified admin';

	/**
	 * Execute the console command.
	 */
	public function handle(): void
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
				options: static fn($value) => mb_strlen($value) > 0
					? Admin::where('username', 'like', "%{$value}%")->pluck('username', 'id')->all()
					: [],
			),
		];
	}
}

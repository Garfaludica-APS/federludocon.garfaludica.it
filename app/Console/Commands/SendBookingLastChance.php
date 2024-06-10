<?php

namespace App\Console\Commands;

use App\Mail\BookingInvitation;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use Illuminate\Support\Facades\Mail;
use function Laravel\Prompts\text;

class SendBookingLastChance extends Command implements PromptsForMissingInput
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = '
		mail:last-chance
					{email : The email of the recipient}
					{name : The name of the recipient}
					{date : The date of the last booking attempt}
	';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send email';

	/**
	 * Execute the console command.
	 */
	public function handle(): void
	{
		$email = $this->argument('email');
		$name = $this->argument('name');
		$date = $this->argument('date');

		Mail::to($email)->send(new BookingInvitation($name, $date));

		$this->info('Mail sent to \'' . $email . '\'.');
	}

	protected function promptForMissingArgumentsUsing(): array
	{
		return [
			'email' => static fn() => text(
				label: 'What is the email of the recipient?',
				placeholder: 'user@example.com',
				validate: ['email' => 'email:strict,dns,spoof|max:254'],
			),
			'name' => static fn() => text(
				label: 'What is the name of the recipient?',
				placeholder: 'John',
			),
			'date' => static fn() => text(
				label: 'What is the date of the last booking attempt?',
				placeholder: 'June 10 2024',
			),
		];
	}
}

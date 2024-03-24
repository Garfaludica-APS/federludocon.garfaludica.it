<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use function Laravel\Prompts\password;
use function Laravel\Prompts\search;

class ChangeAdminPassword extends Command implements PromptsForMissingInput
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'admin:change-password
				{admin : The ID of the admin}
				{password : The new password}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Change password for the specified admin';

	/**
	 * Execute the console command.
	 */
	public function handle(): void
	{
		$password = password(
			label: 'Confirm the new password:',
			placeholder: 'password',
			hint: 'Password must be at least 8 characters long',
			validate: ['password' => 'min:8'],
		);
		if ($password !== $this->argument('password')) {
			$this->error('Passwords do not match');
			return;
		}
		$admin = Admin::find($this->argument('admin'));
		$admin->password = $password;
		$admin->save();
		$this->info('Password changed successfully!');
	}

	protected function promptForMissingArgumentsUsing(): array
	{
		return [
			'admin' => fn () => search(
				label: 'Search for an admin:',
				placeholder: 'E.g. admin',
				options: fn ($value) => strlen($value) > 0
					? Admin::where('username', 'like', "%{$value}%")->pluck('username', 'id')->all()
					: [],
			),
			'password' => fn () => password(
				label: 'What is the new password?',
				placeholder: 'password',
				hint: 'Password must be at least 8 characters long',
				validate: ['password' => 'min:8'],
			),
		];
	}
}

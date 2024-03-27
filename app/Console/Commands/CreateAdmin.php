<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdmin extends Command implements PromptsForMissingInput
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = <<<'EOD'
		admin:create
						{username : The username of the new admin}
						{email : The email of the new admin}
						{password : The password of the new admin}
						{--s|super : Create a super admin}
		EOD;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new admin';

	/**
	 * Execute the console command.
	 */
	public function handle(): void
	{
		$password = password(
			label: 'Confirm the password:',
			placeholder: 'password',
			hint: 'Password must be at least 8 characters long',
			validate: ['password' => 'min:8'],
		);
		if ($password !== $this->argument('password')) {
			$this->error('Passwords do not match');
			return;
		}
		Admin::factory()->create([
			'username' => $this->argument('username'),
			'email' => $this->argument('email'),
			'password' => $password,
			'is_super_admin' => $this->option('super'),
		]);

		$this->info('Admin \'' . $this->argument('username') . '\' created!');
	}

	protected function promptForMissingArgumentsUsing(): array
	{
		return [
			'username' => static fn() => text(
				label: 'What is the username of the new admin?',
				placeholder: 'admin',
				default: 'admin',
				hint: 'Username must be at least 3 characters long',
				validate: ['username' => 'min:3|max:32|unique:admins'],
			),
			'email' => static fn() => text(
				label: 'What is the email of the new admin?',
				placeholder: 'admin@garfaludica.it',
				default: 'admin@garfaludica.it',
				validate: ['email' => 'email:strict,dns,spoof|max:254|unique:admins'],
			),
			'password' => static fn() => password(
				label: 'What is the password of the new admin?',
				placeholder: 'password',
				hint: 'Password must be at least 8 characters long',
				required: true,
				validate: ['password' => 'min:8'],
			),
		];
	}
}

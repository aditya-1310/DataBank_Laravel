<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email?} {name?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update an admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('What is the admin email?', 'admin@example.com');
        $name = $this->argument('name') ?? $this->ask('What is the admin name?', 'Administrator');
        $password = $this->argument('password') ?? $this->secret('What is the admin password?');

        if (!$password) {
            $this->error('Password is required');
            return 1;
        }

        // Check if user exists
        $user = User::where('email', $email)->first();

        if ($user) {
            // Update existing user
            $user->name = $name;
            $user->password = Hash::make($password);
            $user->is_admin = true;
            $user->save();

            $this->info("Admin user updated: {$email}");
        } else {
            // Create new user
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);

            $this->info("Admin user created: {$email}");
        }

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:reset-password {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a user password for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';
        $password = 'password'; // Default test password
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            
            // Ask if user wants to create the user
            if ($this->confirm("Would you like to create this user?")) {
                $user = User::create([
                    'name' => 'Test User',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]);
                $this->info("User created successfully!");
            } else {
                return;
            }
        } else {
            $user->password = Hash::make($password);
            $user->email_verified_at = now(); // Ensure user is verified
            $user->save();
            $this->info("Password updated successfully!");
        }
        
        $this->info("");
        $this->info("Login credentials:");
        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->info("");
    }
}

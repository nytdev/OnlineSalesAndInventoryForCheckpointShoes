<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TestLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test login credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'test@example.com';
        $password = 'password';
        
        $this->info('Testing login credentials...');
        $this->info('Email: ' . $email);
        $this->info('Password: ' . $password);
        
        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error('User not found!');
            return;
        }
        
        $this->info('User found: ' . $user->name);
        
        // Test password
        if (Hash::check($password, $user->password)) {
            $this->info('✓ Password is correct!');
        } else {
            $this->error('✗ Password is incorrect!');
        }
        
        // Test Auth::attempt
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->info('✓ Auth::attempt succeeded!');
            Auth::logout();
        } else {
            $this->error('✗ Auth::attempt failed!');
        }
    }
}

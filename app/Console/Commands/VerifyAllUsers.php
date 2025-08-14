<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:verify-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all existing users as email verified';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying all users...');
        
        $unverifiedUsers = User::whereNull('email_verified_at')->get();
        
        if ($unverifiedUsers->count() === 0) {
            $this->info('No unverified users found.');
            return;
        }
        
        foreach ($unverifiedUsers as $user) {
            $user->email_verified_at = now();
            $user->save();
            $this->info('Verified user: ' . $user->email);
        }
        
        $this->info('Successfully verified ' . $unverifiedUsers->count() . ' users.');
    }
}

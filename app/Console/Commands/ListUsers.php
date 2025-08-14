<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users with their verification status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        if ($users->count() === 0) {
            $this->info('No users found in the database.');
            return;
        }
        
        $this->info('Found ' . $users->count() . ' users:');
        $this->info('----------------------------------------');
        
        foreach ($users as $user) {
            $verified = $user->email_verified_at ? 'Yes (' . $user->email_verified_at->format('Y-m-d H:i:s') . ')' : 'No';
            $this->info('ID: ' . $user->id . ' | Email: ' . $user->email . ' | Name: ' . $user->name . ' | Verified: ' . $verified);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearVerificationLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:clear-logs {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear email verification logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            $this->info('No log file found to clear.');
            return 0;
        }
        
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to clear all logs? This will remove ALL log entries, not just verification emails.')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }
        
        File::put($logPath, '');
        
        $this->info('Log file cleared successfully.');
        
        return 0;
    }
}

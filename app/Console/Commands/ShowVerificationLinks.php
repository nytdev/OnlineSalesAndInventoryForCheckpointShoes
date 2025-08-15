<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ShowVerificationLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verification:show-links {--count=5 : Number of recent verification links to show}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show recent email verification links from logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            $this->error('Log file not found at: ' . $logPath);
            return 1;
        }
        
        $logContent = File::get($logPath);
        $count = $this->option('count');
        
        // Extract verification URLs using regex
        preg_match_all('/verify-email\/\d+\/[a-f0-9]+\?expires=\d+&(?:amp;)?signature=[a-f0-9]+/', $logContent, $matches);
        
        if (empty($matches[0])) {
            $this->info('No verification links found in logs.');
            return 0;
        }
        
        $links = array_slice(array_reverse(array_unique($matches[0])), 0, $count);
        
        $this->info('Recent Email Verification Links:');
        $this->info('========================================');
        
        foreach ($links as $index => $link) {
            $fullUrl = 'http://onlinesalesandinventoryforcheckpoint.test/' . str_replace('&amp;', '&', $link);
            $this->line(($index + 1) . '. ' . $fullUrl);
        }
        
        $this->info('');
        $this->info('Copy any of these links to verify a user account.');
        
        return 0;
    }
}

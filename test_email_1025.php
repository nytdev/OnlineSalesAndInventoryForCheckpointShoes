<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Create a test user
    $user = new App\Models\User();
    $user->name = 'Port 1025 Test User';
    $user->email = 'test-1025@example.com';
    $user->password = bcrypt('password123');
    $user->save();

    echo "Created user: " . $user->email . "\n";
    echo "Sending verification email to port 1025...\n";

    // Send verification email (should go to port 1025)
    $user->sendEmailVerificationNotification();

    echo "✅ Verification email sent successfully to port 1025!\n";
    echo "Check your mail testing tool (MailHog/Mailpit) at http://127.0.0.1:8025\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

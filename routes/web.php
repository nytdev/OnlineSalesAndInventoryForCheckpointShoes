<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Debug route to check authentication and session
Route::get('/debug', function () {
    return [
        'authenticated' => auth()->check(),
        'user' => auth()->user(),
        'session_id' => session()->getId(),
        'intended_url' => session('url.intended'),
        'session_driver' => config('session.driver'),
        'session_domain' => config('session.domain'),
        'csrf_token' => csrf_token(),
        'session_lifetime' => config('session.lifetime'),
        'app_key' => config('app.key') ? 'Set' : 'Not set',
        'session_table_exists' => \Schema::hasTable('sessions'),
        'session_data' => session()->all(),
    ];
});

// Test login route
Route::get('/test-login', function () {
    $user = \App\Models\User::first();
    if ($user) {
        auth()->login($user);
        return redirect()->route('dashboard')->with('success', 'Test login successful!');
    }
    return 'No users found';
});

// CSRF Test routes
Route::get('/csrf-test', function () {
    return view('csrf-test');
});

Route::post('/csrf-test', function () {
    return response()->json(['success' => true, 'message' => 'CSRF token is working!']);
});


// Main Route
Route::get('/dashboard', function () {
    // Debug: Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
    }
    
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});

    // User Management Routes
    Route::resource('/user-management', UserManagementController::class);
    // Route::get('/user-management', [UserManagementController::class,'create'])->name('user-management.create');
    // Route::get('/user-management', [UserManagementController::class,'view'])->name('user-management.show');
    // Route::patch('/user-management', [UserManagementController::class,'edit'])->name('user-management.edit');

require __DIR__.'/auth.php';
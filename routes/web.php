<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryManagementController;
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
Route::get('dashboard', function () {
    // Debug: Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management Routes
    Route::resource('user-management', UserManagementController::class);

    // Customer Management Routes
    Route::prefix('sales/customers')->name('sales.customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        
        // Import and Export routes
        Route::get('/import/form', [CustomerController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [CustomerController::class, 'import'])->name('import.process');
        Route::get('/template/download', [CustomerController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [CustomerController::class, 'export'])->name('export');
        
        // Status toggle and analytics
        Route::post('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/analytics', [CustomerController::class, 'analytics'])->name('analytics');
    });

    //Inventory Routes
    Route::prefix('inventory/products')->name('inventory.products.')->group(function () {
        Route::get('/', [InventoryManagementController::class, 'index'])->name('index');
        Route::get('/create', [InventoryManagementController::class, 'create'])->name('create');
        Route::post('/', [InventoryManagementController::class, 'store'])->name('store');
        Route::get('/{product}', [InventoryManagementController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [InventoryManagementController::class, 'edit'])->name('edit');
        Route::put('/{product}', [InventoryManagementController::class, 'update'])->name('update');
        Route::delete('/{product}', [InventoryManagementController::class, 'destroy'])->name('destroy');
        
        // Import routes
        Route::get('/import/form', [InventoryManagementController::class, 'showImportForm'])->name('import');
        Route::post('/import', [InventoryManagementController::class, 'import'])->name('import');
        Route::get('/template/download', [InventoryManagementController::class, 'downloadTemplate'])->name('template');
        
        // API routes
        Route::post('/bulk-update-stock', [InventoryManagementController::class, 'bulkUpdateStock'])->name('bulk-update-stock');
        Route::get('/alerts', [InventoryManagementController::class, 'getAlertsData'])->name('alerts');
    });
});

require __DIR__.'/auth.php';

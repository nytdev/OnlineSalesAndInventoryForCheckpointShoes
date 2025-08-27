<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use App\Models\Product;
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

// Test dashboard statistics
Route::get('/test-stats', function () {
    try {
        $inventoryStats = [
            'total_products' => \App\Models\Product::count(),
            'low_stock_products' => \App\Models\Product::where('quantity', '<=', 10)->count(),
            'out_of_stock_products' => \App\Models\Product::where('quantity', '<=', 0)->count(),
            'total_inventory_value' => \App\Models\Product::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics working correctly!',
            'data' => $inventoryStats
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'error' => $e->getTraceAsString()
        ]);
    }
});


// Main Route
Route::get('dashboard', function () {
    // Debug: Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
    }

    // Get comprehensive statistics for dashboard with error handling

    // Inventory Statistics
    try {
        $inventoryStats = [
            'total_products' => \App\Models\Product::count(),
            'active_products' => \App\Models\Product::count(), // No status column, so use total count
            'low_stock_products' => \App\Models\Product::where('quantity', '<=', 10)->count(), // Low stock threshold of 10
            'out_of_stock_products' => \App\Models\Product::where('quantity', '<=', 0)->count(),
            'total_inventory_value' => \App\Models\Product::selectRaw('SUM(quantity * price) as total')->value('total') ?? 0,
        ];
    } catch (\Exception $e) {
        $inventoryStats = [
            'total_products' => 0,
            'active_products' => 0,
            'low_stock_products' => 0,
            'out_of_stock_products' => 0,
            'total_inventory_value' => 0,
        ];
    }

    // Sales Statistics
    try {
        $salesStats = [
            'total_sales' => \App\Models\Sale::count(),
            'today_sales' => \App\Models\Sale::whereDate('created_at', today())->count(),
            'this_month_sales' => \App\Models\Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'total_sales_value' => \App\Models\Sale::sum('total_amount') ?? 0,
            'today_sales_value' => \App\Models\Sale::whereDate('created_at', today())->sum('total_amount') ?? 0,
        ];
    } catch (\Exception $e) {
        $salesStats = [
            'total_sales' => 0,
            'today_sales' => 0,
            'this_month_sales' => 0,
            'total_sales_value' => 0,
            'today_sales_value' => 0,
        ];
    }

    // Customer Statistics
    try {
        $customerStats = [
            'total_customers' => \App\Models\Customer::count(),
            'active_customers' => \App\Models\Customer::where('status', 'active')->count(),
            'new_customers_this_month' => \App\Models\Customer::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];
    } catch (\Exception $e) {
        $customerStats = [
            'total_customers' => 0,
            'active_customers' => 0,
            'new_customers_this_month' => 0,
        ];
    }

    // Purchase Statistics
    try {
        $purchaseStats = [
            'total_purchases' => \App\Models\Purchase::count(),
            'this_month_purchases' => \App\Models\Purchase::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'total_purchase_value' => \App\Models\Purchase::sum('total_amount') ?? 0,
        ];
    } catch (\Exception $e) {
        $purchaseStats = [
            'total_purchases' => 0,
            'this_month_purchases' => 0,
            'total_purchase_value' => 0,
        ];
    }

    // Return Statistics
    try {
        $returnStats = [
            'total_returns' => \App\Models\Returns::count(),
            'pending_returns' => \App\Models\Returns::pending()->count(),
            'approved_returns' => \App\Models\Returns::approved()->count(),
            'total_return_value' => \App\Models\Returns::approved()->get()->sum('total_amount') ?? 0,
            'today_returns' => \App\Models\Returns::today()->count(),
            'this_week_returns' => \App\Models\Returns::thisWeek()->count(),
            'this_month_returns' => \App\Models\Returns::thisMonth()->count(),
        ];
    } catch (\Exception $e) {
        $returnStats = [
            'total_returns' => 0,
            'pending_returns' => 0,
            'approved_returns' => 0,
            'total_return_value' => 0,
            'today_returns' => 0,
            'this_week_returns' => 0,
            'this_month_returns' => 0,
        ];
    }

    // Supplier Statistics
    try {
        $supplierStats = [
            'total_suppliers' => \App\Models\Supplier::count(),
            'active_suppliers' => \App\Models\Supplier::where('status', 'active')->count(),
        ];
    } catch (\Exception $e) {
        $supplierStats = [
            'total_suppliers' => 0,
            'active_suppliers' => 0,
        ];
    }

    return view('dashboard', compact(
        'inventoryStats',
        'salesStats',
        'customerStats',
        'purchaseStats',
        'returnStats',
        'supplierStats'
    ));
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

    //Inventory Routes for products
    Route::prefix('inventory/products')->name('inventory.products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

        // Import routes
        Route::get('/import/form', [ProductController::class, 'showImportForm'])->name('import');
        Route::post('/import', [ProductController::class, 'import'])->name('import');
        Route::get('/template/download', [ProductController::class, 'downloadTemplate'])->name('template');

        // API routes
        Route::post('/bulk-update-stock', [ProductController::class, 'bulkUpdateStock'])->name('bulk-update-stock');
        Route::get('/alerts', [ProductController::class, 'getAlertsData'])->name('alerts');
    });

    // Supplier Management Routes
    Route::prefix('inventory/suppliers')->name('inventory.suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');

        // Import and Export routes
        Route::get('/import/form', [SupplierController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [SupplierController::class, 'import'])->name('import.process');
        Route::get('/template/download', [SupplierController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [SupplierController::class, 'export'])->name('export');

        // Status toggle and analytics
        Route::post('/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/analytics', [SupplierController::class, 'analytics'])->name('analytics');
        Route::get('/alerts', [SupplierController::class, 'getAlertsData'])->name('alerts');
    });

    // Stock Management Routes
    Route::prefix('inventory/stock')->name('inventory.stock.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/create', [StockController::class, 'create'])->name('create');
        Route::post('/', [StockController::class, 'store'])->name('store');
        Route::get('/{stock}', [StockController::class, 'show'])->name('show');
        Route::get('/{stock}/edit', [StockController::class, 'edit'])->name('edit');
        Route::put('/{stock}', [StockController::class, 'update'])->name('update');
        Route::delete('/{stock}', [StockController::class, 'destroy'])->name('destroy');

        // Stock movement operations
        Route::post('/{stock}/confirm', [StockController::class, 'confirm'])->name('confirm');
        
        // Transfer operations
        Route::get('/transfer/form', [StockController::class, 'showTransferForm'])->name('transfer.form');
        Route::post('/transfer/process', [StockController::class, 'processTransfer'])->name('transfer.process');
        
        // Waste/damage operations
        Route::get('/waste/form', [StockController::class, 'showWasteForm'])->name('waste.form');
        Route::post('/waste/process', [StockController::class, 'processWaste'])->name('waste.process');
        
        // Import and Export routes
        Route::get('/import/form', [StockController::class, 'showImportForm'])->name('import');
        Route::post('/import/process', [StockController::class, 'import'])->name('import.process');
        Route::get('/template/download', [StockController::class, 'downloadTemplate'])->name('template');
        Route::get('/export', [StockController::class, 'export'])->name('export');
        
        // Analytics and reporting
        Route::get('/analytics', [StockController::class, 'analytics'])->name('analytics');
        Route::get('/product/{product}/history', [StockController::class, 'productHistory'])->name('product.history');
        Route::get('/alerts', [StockController::class, 'getAlertsData'])->name('alerts');
    });

    // Sales Order Management Routes
    Route::prefix('sales/orders')->name('sales.orders.')->group(function () {
        Route::get('/', [SalesOrderController::class, 'index'])->name('index');
        Route::get('/create', [SalesOrderController::class, 'create'])->name('create');
        Route::post('/', [SalesOrderController::class, 'store'])->name('store');
        Route::get('/{order}', [SalesOrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [SalesOrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [SalesOrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [SalesOrderController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{order}/change-status', [SalesOrderController::class, 'changeStatus'])->name('change-status');
        Route::post('/{order}/fulfill', [SalesOrderController::class, 'fulfill'])->name('fulfill');

        // Analytics
        Route::get('/analytics', [SalesOrderController::class, 'analytics'])->name('analytics');
    });

    // Returns Management Routes
    Route::prefix('sales/returns')->name('sales.returns.')->group(function () {
        Route::get('/', [ReturnsController::class, 'index'])->name('index');
        Route::get('/create', [ReturnsController::class, 'create'])->name('create');
        Route::post('/', [ReturnsController::class, 'store'])->name('store');
        Route::get('/{return}', [ReturnsController::class, 'show'])->name('show');
        Route::get('/{return}/edit', [ReturnsController::class, 'edit'])->name('edit');
        Route::put('/{return}', [ReturnsController::class, 'update'])->name('update');
        Route::delete('/{return}', [ReturnsController::class, 'destroy'])->name('destroy');

        // Status management routes
        Route::post('/{return}/approve', [ReturnsController::class, 'approve'])->name('approve');
        Route::post('/{return}/reject', [ReturnsController::class, 'reject'])->name('reject');
        Route::post('/{return}/mark-as-processed', [ReturnsController::class, 'markAsProcessed'])->name('mark-as-processed');

        // Bulk operations
        Route::post('/bulk-approve', [ReturnsController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ReturnsController::class, 'bulkReject'])->name('bulk-reject');

        // Analytics
        Route::get('/analytics', [ReturnsController::class, 'analytics'])->name('analytics');
    });
});

require __DIR__ . '/auth.php';

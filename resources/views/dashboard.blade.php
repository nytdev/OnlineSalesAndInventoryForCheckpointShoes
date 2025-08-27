<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="pt-0.5 h-screen overflow-hidden" x-data="{ navOpen: true, inventoryOpen: false }">
        <div class="flex h-screen justify-stretch">
            <div class="flex flex-row lg:flex-row w-full">
                <!-- Navigation Pane -->
                <div x-show="navOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full" :class="navOpen ? 'w-50' : 'w-0'"
                    class="h-full bg-white dark:bg-gray-800 shadow-lg relative flex-shrink-0">

                    <!-- Logo Section -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center space-x-3">
                            <!-- Logo Icon -->
                            <div class="bg-blue-600 p-2 rounded-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>

                            <!-- Logo Text -->
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Checkpoint</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mx-auto">Sales and Inventory</p>
                            </div>
                        </div>

                        <!-- Arrow Toggle Button -->
                        <button @click="navOpen = false"
                            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                            {{-- ⬅️ --}}
                        </button>
                    </div>

                    <!-- Navigation Menu -->
                    <div class="flex-2 overflow-y-auto">
                        <div class="p-3">
                            <h3
                                class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                                Main Menu</h3>
                            <nav class="space-y-2">
                                <!-- Home/Dashboard -->
                                <x-nav-item 
                                    route="dashboard" 
                                    :icon="App\Helpers\NavigationHelper::getIcon('dashboard')" 
                                    title="Dashboard" 
                                />

                                <!-- Inventory -->
                                <x-nav-item 
                                    route-pattern="inventory.*" 
                                    :icon="App\Helpers\NavigationHelper::getIcon('inventory')" 
                                    title="Inventory" 
                                    :is-dropdown="true">
                                    
                                    <!-- Products -->
                                    <x-nav-item 
                                        route="inventory.products.index" 
                                        route-pattern="inventory.products.*"
                                        :icon="App\Helpers\NavigationHelper::getIcon('products', 'w-4 h-4 mr-3')" 
                                        title="Products" 
                                        size="small"
                                    />

                                    <!-- Composite Products -->
                                    <x-nav-item 
                                        href="#"
                                        :icon="App\Helpers\NavigationHelper::getIcon('composite-products', 'w-4 h-4 mr-3')" 
                                        title="Composite Products" 
                                        size="small"
                                    />

                                    <!-- Stock Management -->
                                    <x-nav-item 
                                        route="inventory.stock.index" 
                                        route-pattern="inventory.stock.*"
                                        :icon="App\Helpers\NavigationHelper::getIcon('stock-adjustment', 'w-4 h-4 mr-3')" 
                                        title="Stock Adjustment" 
                                        size="small"
                                    />
                                </x-nav-item>

                                <!-- Sales -->
                                <x-nav-item 
                                    route-pattern="sales.*" 
                                    :icon="App\Helpers\NavigationHelper::getIcon('sales')" 
                                    title="Sales" 
                                    :is-dropdown="true">
                                    
                                    <!-- Customers -->
                                    <x-nav-item 
                                        route="sales.customers.index" 
                                        route-pattern="sales.customers.*"
                                        :icon="App\Helpers\NavigationHelper::getIcon('customers', 'w-4 h-4 mr-3')" 
                                        title="Customers" 
                                        size="small"
                                    />

                                    <!-- Sales Order -->
                                    <x-nav-item 
                                        route="sales.orders.index" 
                                        route-pattern="sales.orders.*"
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' 
                                        title="Sales Order" 
                                        size="small"
                                    />

                                    <!-- Packages -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>' 
                                        title="Packages" 
                                        size="small"
                                    />

                                    <!-- Shipments -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' 
                                        title="Shipments" 
                                        size="small"
                                    />

                                    <!-- Invoices -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' 
                                        title="Invoices" 
                                        size="small"
                                    />

                                    <!-- Payments Received -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' 
                                        title="Payments Received" 
                                        size="small"
                                    />

                                    <!-- Sales Return -->
                                    <x-nav-item 
                                        route="sales.returns.index" 
                                        route-pattern="sales.returns.*"
                                        :icon="App\Helpers\NavigationHelper::getIcon('returns', 'w-4 h-4 mr-3')" 
                                        title="Sales Return" 
                                        size="small"
                                    />

                                    <!-- Exchange -->
                                    <x-nav-item 
                                        href="" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>' 
                                        title="Exchange" 
                                        size="small"
                                    />
                                </x-nav-item>

                                <!-- Purchases -->
                                <x-nav-item 
                                    route-pattern="purchases.*" 
                                    :icon="App\Helpers\NavigationHelper::getIcon('purchases')" 
                                    title="Purchases" 
                                    :is-dropdown="true">
                                    
                                    <!-- Vendor/Supplier -->
                                    <x-nav-item 
                                        route="inventory.suppliers.index" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>' 
                                        title="Vendor/Supplier" 
                                        size="small"
                                    />

                                    <!-- Purchase Order -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>' 
                                        title="Purchase Order" 
                                        size="small"
                                    />

                                    <!-- Purchase Receives -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>' 
                                        title="Purchase Receives" 
                                        size="small"
                                    />

                                    <!-- Purchase Return -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10l-8 8v5h-2v-5l-8-8V2h18v8z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16l-6-6h12l-6 6z"></path></svg>' 
                                        title="Purchase Return" 
                                        size="small"
                                    />

                                    <!-- Payments Made -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>' 
                                        title="Payments Made" 
                                        size="small"
                                    />

                                    <!-- Bills -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' 
                                        title="Bills" 
                                        size="small"
                                    />
                                </x-nav-item>

                                <!-- Reports -->
                                <x-nav-item 
                                    route-pattern="reports.*" 
                                    :icon="App\Helpers\NavigationHelper::getIcon('reports')" 
                                    title="Reports" 
                                    :is-dropdown="true">
                                    
                                    <!-- Sales Report -->
                                    <x-nav-item 
                                        href="#" 
                                        :icon="App\Helpers\NavigationHelper::getIcon('sales', 'w-4 h-4 mr-3')" 
                                        title="Sales Report" 
                                        size="small"
                                    />

                                    <!-- Purchases Report -->
                                    <x-nav-item 
                                        href="#" 
                                        :icon="App\Helpers\NavigationHelper::getIcon('purchases', 'w-4 h-4 mr-3')" 
                                        title="Purchases Report" 
                                        size="small"
                                    />

                                    <!-- Inventory Report -->
                                    <x-nav-item 
                                        href="#" 
                                        :icon="App\Helpers\NavigationHelper::getIcon('inventory', 'w-4 h-4 mr-3')" 
                                        title="Inventory Report" 
                                        size="small"
                                    />

                                    <!-- Returns & Exchange Lists -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>' 
                                        title="Returns & Exchange Lists" 
                                        size="small"
                                    />

                                    <!-- Supplier Report -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>' 
                                        title="Supplier Report" 
                                        size="small"
                                    />

                                    <!-- Profit & Loss -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>' 
                                        title="Profit & Loss" 
                                        size="small"
                                    />

                                    <!-- Income Report -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' 
                                        title="Income Report" 
                                        size="small"
                                    />

                                    <!-- Expense Report -->
                                    <x-nav-item 
                                        href="#" 
                                        icon='<svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' 
                                        title="Expense Report" 
                                        size="small"
                                    />
                                </x-nav-item>

                                <!-- Integration -->
                                <x-nav-item 
                                    href="#" 
                                    :icon="App\Helpers\NavigationHelper::getIcon('integration')" 
                                    title="Integration" 
                                />
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Show Navigation Button (when hidden) -->
                <div x-show="!navOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="fixed top-4 left-4 pr-4">
                    <button @click="navOpen = true">
                    {{-- <button @click="navOpen = true"
                        class="p-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg transition-colors duration-200"> --}}
                        {{-- <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg> --}}
                        ➡️
                    </button>
                </div>

                <!-- Main Content Area -->
                <div :class="navOpen ? 'flex-1' : 'w-full'" class="h-full overflow-y-auto">
                    <div class="bg-white dark:bg-gray-800 min-h-full flex flex-col">
                        <div class="flex-1 p-6 text-gray-900 dark:text-gray-100">
                            <h2 class="text-2xl font-bold mb-4">Welcome</h2>

                            <!-- Dashboard Stats Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                                <!-- Inventory Card -->
                                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-500 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Products
                                            </p>
                                            <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ $inventoryStats['total_products'] ?? 0 }}</p>
                                            <p class="text-xs text-blue-500 dark:text-blue-300 mt-1">
                                                {{ $inventoryStats['low_stock_products'] ?? 0 }} low stock
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sales Card -->
                                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-green-500 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-green-600 dark:text-green-400">Sales
                                            </p>
                                            <p class="text-2xl font-semibold text-green-900 dark:text-green-100">{{ $salesStats['total_sales'] ?? 0 }}</p>
                                            <p class="text-xs text-green-500 dark:text-green-300 mt-1">
                                                ₱{{ number_format($salesStats['total_sales_value'] ?? 0, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customers Card -->
                                <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-purple-500 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                                Customers</p>
                                            <p class="text-2xl font-semibold text-purple-900 dark:text-purple-100">{{ $customerStats['total_customers'] ?? 0 }}</p>
                                            <p class="text-xs text-purple-500 dark:text-purple-300 mt-1">
                                                {{ $customerStats['active_customers'] ?? 0 }} active
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Returns Card -->
                                <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-red-500 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                                Returns</p>
                                            <p class="text-2xl font-semibold text-red-900 dark:text-red-100">{{ $returnStats['total_returns'] ?? 0 }}</p>
                                            <p class="text-xs text-red-500 dark:text-red-300 mt-1">
                                                {{ $returnStats['pending_returns'] ?? 0 }} pending
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Secondary Stats Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                                <!-- Purchases Card -->
                                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-yellow-500 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8.5m-8.5 0a2 2 0 11-4 0 2 2 0 014 0zm8.5 0a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">
                                                Purchases</p>
                                            <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">{{ $purchaseStats['total_purchases'] ?? 0 }}</p>
                                            <p class="text-xs text-yellow-500 dark:text-yellow-300 mt-1">
                                                ₱{{ number_format($purchaseStats['total_purchase_value'] ?? 0, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Suppliers Card -->
                                <div class="bg-indigo-50 dark:bg-indigo-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-indigo-500 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                Suppliers</p>
                                            <p class="text-2xl font-semibold text-indigo-900 dark:text-indigo-100">{{ $supplierStats['total_suppliers'] ?? 0 }}</p>
                                            <p class="text-xs text-indigo-500 dark:text-indigo-300 mt-1">
                                                {{ $supplierStats['active_suppliers'] ?? 0 }} active
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventory Value Card -->
                                <div class="bg-teal-50 dark:bg-teal-900 p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-teal-500 rounded-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-teal-600 dark:text-teal-400">
                                                Inventory Value</p>
                                            <p class="text-2xl font-semibold text-teal-900 dark:text-teal-100">
                                                ₱{{ number_format($inventoryStats['total_inventory_value'] ?? 0, 2) }}</p>
                                            <p class="text-xs text-teal-500 dark:text-teal-300 mt-1">
                                                {{ $inventoryStats['out_of_stock_products'] ?? 0 }} out of stock
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Management Section -->
                            {{-- <div class="mt-8">
                                <h3 class="text-lg font-semibold mb-4">Quick Access</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- User Management Card -->
                                    <a href="{{ route('user-management.index') }}"
                                        class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900 dark:to-blue-900 p-6 rounded-lg hover:shadow-lg transition-all duration-200 border border-indigo-100 dark:border-indigo-800 hover:border-indigo-200 dark:hover:border-indigo-700">
                                        <div class="flex items-center">
                                            <div class="p-3 bg-indigo-500 rounded-lg">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100">
                                                    User Management</h4>
                                                <p class="text-sm text-indigo-600 dark:text-indigo-300">Manage system
                                                    users and permissions</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center text-indigo-600 dark:text-indigo-300">
                                            <span class="text-sm">Access User Management</span>
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            </div> --}}
                        </div>

                        <!-- Footer -->
                        <footer
                            class="mt-auto pt-2 pb-4 px-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <div class="flex flex-col md:flex-row justify-between items-center">
                                <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6">
                                    <!-- Company Info -->
                                    <div class="flex items-center space-x-2">
                                        <div class="bg-blue-600 p-1.5 rounded-md">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Checkpoint
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Sales & Inventory System
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Quick Links -->
                                    <div
                                        class="hidden md:flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                        <a href="#"
                                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                                        <a href="#"
                                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Support</a>
                                        <a href="#"
                                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                                        <a href="#"
                                            class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy</a>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0">
                                    <!-- Version Info -->
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">v1.0.0</span>
                                    </div>

                                    <!-- Copyright -->
                                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                        <p>&copy; {{ date('Y') }} Checkpoint. All Rights Reserved.</p>
                                        <p class="mt-1">Built with ❤️</p>
                                    </div>

                                    <!-- Social Links -->
                                    <div class="flex items-center space-x-2">
                                        <a href="#"
                                            class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </a>

                                        <a href="#"
                                            class="text-gray-400 hover:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84">
                                                </path>
                                            </svg>
                                        </a>

                                        <a href="#"
                                            class="text-gray-400 hover:text-purple dark:hover:text-purple-400 transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </a>

                                    </div>

                                </div>
                            </div>

                            <!-- Mobile Quick Links -->
                            <div class="md:hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-center space-x-6 text-xs text-gray-500 dark:text-gray-400">
                                    <a href="#"
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
                                    <a href="#"
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Support</a>
                                    <a href="#"
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Documentation</a>
                                    <a href="#"
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Privacy</a>
                                </div>
                            </div>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
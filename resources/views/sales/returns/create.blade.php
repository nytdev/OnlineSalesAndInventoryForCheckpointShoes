<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Return</h2>
                            <p class="text-gray-600 dark:text-gray-400">Add a new product return to the system</p>
                        </div>
                        <div>
                            <a href="{{ route('sales.returns.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Returns
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Return Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('sales.returns.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Selection -->
                            <div>
                                <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Product <span class="text-red-500">*</span>
                                </label>
                                <select id="product_id" name="product_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select a product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->product_id }}" 
                                                data-price="{{ $product->selling_price }}"
                                                data-stock="{{ $product->stock_quantity }}"
                                                {{ old('product_id') == $product->product_id ? 'selected' : '' }}>
                                            {{ $product->product_name }} (SKU: {{ $product->sku }}) - Stock: {{ $product->stock_quantity }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Return Date -->
                            <div>
                                <label for="return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Return Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="return_date" name="return_date" 
                                       value="{{ old('return_date', now()->toDateString()) }}" required
                                       max="{{ now()->toDateString() }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('return_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="quantity" name="quantity" 
                                       value="{{ old('quantity') }}" required min="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500" id="stock-info"></p>
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Unit Price <span class="text-red-500">*</span>
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" id="price" name="price" 
                                           value="{{ old('price') }}" required min="0" step="0.01"
                                           class="mt-1 block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Return Status -->
                            <div>
                                <label for="return_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select id="return_status" name="return_status" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" 
                                                {{ old('return_status', 'pending') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('return_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Amount (calculated) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Total Amount
                                </label>
                                <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-300 dark:border-gray-600">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white" id="total-amount">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Return Reason (Optional)
                            </label>
                            <textarea id="reason" name="reason" rows="3" 
                                      placeholder="Enter the reason for this return..."
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('sales.returns.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dynamic calculations -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('product_id');
            const priceInput = document.getElementById('price');
            const quantityInput = document.getElementById('quantity');
            const totalAmountSpan = document.getElementById('total-amount');
            const stockInfo = document.getElementById('stock-info');

            function updatePrice() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption && selectedOption.dataset.price) {
                    priceInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
                    updateStockInfo();
                } else {
                    priceInput.value = '';
                    stockInfo.textContent = '';
                }
                calculateTotal();
            }

            function updateStockInfo() {
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption && selectedOption.dataset.stock) {
                    const stock = selectedOption.dataset.stock;
                    stockInfo.textContent = `Available stock: ${stock} units`;
                    quantityInput.max = stock;
                } else {
                    stockInfo.textContent = '';
                    quantityInput.removeAttribute('max');
                }
            }

            function calculateTotal() {
                const price = parseFloat(priceInput.value) || 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const total = price * quantity;
                totalAmountSpan.textContent = '$' + total.toFixed(2);
            }

            // Event listeners
            productSelect.addEventListener('change', updatePrice);
            priceInput.addEventListener('input', calculateTotal);
            quantityInput.addEventListener('input', calculateTotal);

            // Initial calculation
            if (productSelect.value) {
                updatePrice();
            }
        });
    </script>
</x-app-layout>

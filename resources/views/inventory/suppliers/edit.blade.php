<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Supplier</h2>
                            <p class="text-gray-600 dark:text-gray-400">Update supplier information</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('inventory.suppliers.show', $supplier) }}" 
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Details
                            </a>
                            <a href="{{ route('inventory.suppliers.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Suppliers
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('inventory.suppliers.update', $supplier) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Supplier Name -->
                                <div>
                                    <x-input-label for="supplier_name" :value="__('Supplier Name')" />
                                    <x-text-input id="supplier_name" name="supplier_name" type="text" 
                                                  class="mt-1 block w-full" :value="old('supplier_name', $supplier->supplier_name)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('supplier_name')" />
                                </div>

                                <!-- Contact Number -->
                                <div>
                                    <x-input-label for="supplier_contact" :value="__('Contact Number')" />
                                    <x-text-input id="supplier_contact" name="supplier_contact" type="text" 
                                                  class="mt-1 block w-full" :value="old('supplier_contact', $supplier->supplier_contact)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('supplier_contact')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email Address')" />
                                    <x-text-input id="email" name="email" type="email" 
                                                  class="mt-1 block w-full" :value="old('email', $supplier->email)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Supplier Type -->
                                <div>
                                    <x-input-label for="type" :value="__('Supplier Type')" />
                                    <select id="type" name="type" 
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">Select Type</option>
                                        <option value="local" {{ old('type', $supplier->type) === 'local' ? 'selected' : '' }}>Local</option>
                                        <option value="international" {{ old('type', $supplier->type) === 'international' ? 'selected' : '' }}>International</option>
                                        <option value="distributor" {{ old('type', $supplier->type) === 'distributor' ? 'selected' : '' }}>Distributor</option>
                                        <option value="manufacturer" {{ old('type', $supplier->type) === 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                        <option value="service_provider" {{ old('type', $supplier->type) === 'service_provider' ? 'selected' : '' }}>Service Provider</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Address -->
                                <div class="md:col-span-2">
                                    <x-input-label for="address" :value="__('Street Address')" />
                                    <x-text-input id="address" name="address" type="text" 
                                                  class="mt-1 block w-full" :value="old('address', $supplier->address)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <!-- City -->
                                <div>
                                    <x-input-label for="city" :value="__('City')" />
                                    <x-text-input id="city" name="city" type="text" 
                                                  class="mt-1 block w-full" :value="old('city', $supplier->city)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>

                                <!-- State -->
                                <div>
                                    <x-input-label for="state" :value="__('State/Province')" />
                                    <x-text-input id="state" name="state" type="text" 
                                                  class="mt-1 block w-full" :value="old('state', $supplier->state)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('state')" />
                                </div>

                                <!-- Postal Code -->
                                <div>
                                    <x-input-label for="postal_code" :value="__('Postal Code')" />
                                    <x-text-input id="postal_code" name="postal_code" type="text" 
                                                  class="mt-1 block w-full" :value="old('postal_code', $supplier->postal_code)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
                                </div>

                                <!-- Country -->
                                <div>
                                    <x-input-label for="country" :value="__('Country')" />
                                    <x-text-input id="country" name="country" type="text" 
                                                  class="mt-1 block w-full" :value="old('country', $supplier->country)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('country')" />
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tax ID -->
                                <div>
                                    <x-input-label for="tax_id" :value="__('Tax ID/Registration Number')" />
                                    <x-text-input id="tax_id" name="tax_id" type="text" 
                                                  class="mt-1 block w-full" :value="old('tax_id', $supplier->tax_id)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('tax_id')" />
                                </div>

                                <!-- Payment Terms -->
                                <div>
                                    <x-input-label for="payment_terms" :value="__('Payment Terms')" />
                                    <x-text-input id="payment_terms" name="payment_terms" type="text" 
                                                  class="mt-1 block w-full" :value="old('payment_terms', $supplier->payment_terms)" 
                                                  placeholder="e.g., Net 30, COD, Prepaid" />
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_terms')" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" 
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="active" {{ old('status', $supplier->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $supplier->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="4" 
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                      placeholder="Additional notes about the supplier...">{{ old('notes', $supplier->notes) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('inventory.suppliers.show', $supplier) }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Supplier
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

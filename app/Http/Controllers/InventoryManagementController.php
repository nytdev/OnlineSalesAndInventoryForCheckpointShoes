<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Imports\ProductsImport;

class InventoryManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by brand
        if ($request->has('brand') && $request->brand) {
            $query->where('product_brand', 'like', '%' . $request->brand . '%');
        }

        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        // Price range filters
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort', 'product_name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15)->withQueryString();

        // Get unique brands for filter dropdown
        $brands = Product::distinct()->pluck('product_brand')->filter()->sort();

        return view('inventory.products.index', compact('products', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_brand' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        Product::create($data);

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Load relationships for detailed view
        $product->load(['sales', 'purchases', 'returns']);
        
        // Calculate additional metrics
        $stockMovement = $product->stock_movement;
        $recentSales = $product->sales()->latest()->take(5)->get();
        $recentPurchases = $product->purchases()->latest()->take(5)->get();

        return view('inventory.products.show', compact('product', 'stockMovement', 'recentSales', 'recentPurchases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('inventory.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_brand' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete associated image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('inventory.products.import');
    }

    /**
     * Import products from Excel file
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $import = new ProductsImport();
            $import->import($request->file('excel_file'));

            $importedCount = $import->getRowCount();
            
            return redirect()->route('inventory.products.index')
                ->with('success', "Successfully imported {$importedCount} products!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="products_template.xlsx"',
        ];

        // Create sample data for template
        $sampleData = [
            ['Product Name', 'Brand', 'Quantity', 'Price', 'Description'],
            ['Sample Product 1', 'Sample Brand', 100, 29.99, 'Sample description'],
            ['Sample Product 2', 'Another Brand', 50, 49.99, 'Another description'],
        ];

        $callback = function() use ($sampleData) {
            $file = fopen('php://output', 'w');
            
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk update stock quantities
     */
    public function bulkUpdateStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array',
            'updates.*.product_id' => 'required|exists:products,product_id',
            'updates.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $updates = [];
        foreach ($request->updates as $update) {
            $updates[$update['product_id']] = $update['quantity'];
        }

        $results = Product::bulkUpdateStock($updates);
        $successCount = array_sum(array_map('intval', $results));

        return response()->json([
            'success' => true,
            'message' => "Updated stock for {$successCount} products",
            'results' => $results,
        ]);
    }

    /**
     * Get products that need attention (low stock, etc.)
     */
    public function getAlertsData()
    {
        $lowStockProducts = Product::needsReordering(10);
        $outOfStockProducts = Product::outOfStock();

        return response()->json([
            'low_stock_count' => $lowStockProducts->count(),
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_count' => $outOfStockProducts->count(),
            'out_of_stock_products' => $outOfStockProducts,
            'total_products' => Product::count(),
            'total_inventory_value' => Product::totalInventoryValue(),
        ]);
    }
}

# Product Module Documentation

This document outlines the comprehensive functionality that has been added to the Product module for the Online Sales and Inventory system.

## Overview

The Product module now includes full-featured models for Product, Sale, Purchase, and Returns, along with a ProductService class that provides high-level business logic operations.

## Models

### Product Model

**File**: `app/Models/Product.php`

#### Key Features
- **Fillable Attributes**: product_name, product_brand, quantity, price
- **Primary Key**: product_id
- **Relationships**: sales, purchases, returns
- **Automatic Casting**: quantity (integer), price (decimal:2)

#### Methods
- `isInStock($quantity)` - Check if product has sufficient stock
- `isLowStock($threshold)` - Check if product is below stock threshold
- `decreaseStock($quantity)` - Reduce stock after a sale
- `increaseStock($quantity)` - Increase stock after purchase/return
- `updatePrice($newPrice)` - Update product price

#### Scopes
- `lowStock($threshold)` - Find products below stock threshold
- `search($term)` - Search by product name or brand
- `inStock()` - Products with stock > 0
- `outOfStock()` - Products with stock <= 0

#### Attributes
- `full_name` - Combines product_name and product_brand
- `total_sold` - Total quantity sold
- `total_purchased` - Total quantity purchased
- `total_returned` - Total quantity returned
- `total_revenue` - Total revenue from sales
- `inventory_value` - Current stock value
- `profit_margin` - Profit margin based on purchase prices
- `stock_turnover` - Stock turnover rate
- `stock_movement` - Complete history of stock movements

#### Static Methods
- `needsReordering($threshold)` - Get products needing restock
- `outOfStock()` - Get out of stock products
- `totalInventoryValue()` - Total value of all inventory
- `bulkUpdateStock($updates)` - Update multiple products' stock
- `getByBrand($brand)` - Get products by brand
- `topRevenueProducts($limit)` - Top revenue generating products
- `oversoldProducts()` - Products with negative stock

### Sale Model

**File**: `app/Models/Sale.php`

#### Key Features
- **Fillable Attributes**: product_id, quantity, date
- **Primary Key**: sales_id
- **Relationships**: product
- **Automatic Casting**: quantity (integer), date (datetime)

#### Methods
- `processSale($productId, $quantity, $date)` - Process a sale and update inventory

#### Scopes
- `today()` - Sales from today
- `thisWeek()` - Sales from this week
- `thisMonth()` - Sales from this month
- `dateRange($start, $end)` - Sales from date range

#### Attributes
- `total_amount` - Sale total (quantity × product price)

#### Static Methods
- `totalSalesAmount($startDate, $endDate)` - Total sales revenue
- `topSellingProducts($limit)` - Most sold products

### Purchase Model

**File**: `app/Models/Purchase.php`

#### Key Features
- **Fillable Attributes**: supplier_id, product_id, user_id, price, quantity, purchase_date
- **Primary Key**: purchase_id
- **Relationships**: product, supplier, user
- **Automatic Casting**: quantity (integer), price (decimal:2), purchase_date (datetime)

#### Methods
- `processPurchase($supplierId, $productId, $userId, $price, $quantity, $date)` - Process purchase and update inventory

#### Scopes
- `today()` - Purchases from today
- `thisWeek()` - Purchases from this week
- `thisMonth()` - Purchases from this month
- `dateRange($start, $end)` - Purchases from date range
- `bySupplier($supplierId)` - Purchases from specific supplier

#### Attributes
- `total_amount` - Purchase total (quantity × price)

#### Static Methods
- `totalPurchaseAmount($startDate, $endDate)` - Total purchase costs
- `purchasesBySupplier()` - Purchase statistics by supplier
- `mostPurchasedProducts($limit)` - Most purchased products

### Returns Model

**File**: `app/Models/Returns.php`

#### Key Features
- **Fillable Attributes**: product_id, quantity, return_status, price, return_date
- **Primary Key**: return_id
- **Relationships**: product
- **Automatic Casting**: quantity (integer), price (decimal:2), return_date (datetime)
- **Status Constants**: PENDING, APPROVED, REJECTED, PROCESSED, REFUNDED

#### Methods
- `processReturn($productId, $quantity, $price, $status, $date)` - Create a return record
- `approve()` - Approve return and add stock back
- `reject()` - Reject the return
- `markAsProcessed()` - Mark as processed
- `isPending()`, `isApproved()`, `isProcessed()` - Status checks

#### Scopes
- `today()` - Returns from today
- `thisWeek()` - Returns from this week
- `thisMonth()` - Returns from this month
- `byStatus($status)` - Returns with specific status
- `pending()` - Pending returns only
- `approved()` - Approved returns only

#### Attributes
- `total_amount` - Return total (quantity × price)

#### Static Methods
- `getStatuses()` - All available status values
- `totalReturnAmount($startDate, $endDate)` - Total return amounts
- `mostReturnedProducts($limit)` - Products with most returns
- `returnsByStatus()` - Return statistics by status

## ProductService Class

**File**: `app/Services/ProductService.php`

The ProductService provides high-level business operations:

### Methods

#### `getInventoryDashboard()`
Returns comprehensive inventory overview:
- Total products count
- Low stock products count and list
- Out of stock products count and list
- Total inventory value
- Average product value

#### `processSale($productId, $quantity, $date)`
Handles sale processing with validation:
- Checks product existence
- Validates stock availability
- Processes sale and updates inventory
- Returns detailed result with success/error info

#### `processPurchase($supplierId, $productId, $userId, $price, $quantity, $date)`
Handles purchase processing:
- Creates purchase record
- Updates inventory
- Updates product price if different
- Returns detailed result

#### `processReturn($productId, $quantity, $price, $status, $date)`
Handles return processing:
- Creates return record
- Returns detailed result with return info

#### `getSalesAnalytics($startDate, $endDate)`
Provides sales analytics:
- Total sales count
- Total revenue
- Average sale amount
- Top selling products
- Sales grouped by date

#### `getProductsNeedingAttention()`
Identifies products requiring attention:
- Low stock products
- Out of stock products
- Oversold products (negative stock)
- Products with high return rates

#### `bulkUpdateStock($stockUpdates)`
Updates multiple products' stock levels:
- Takes array of productId => quantity
- Returns success/failure details
- Provides count of successful updates

#### `searchProducts($filters)`
Advanced product search with filtering:
- Search by name/brand
- Filter by price range
- Filter by stock range
- Filter by stock status
- Sortable results

## Database Migrations

### Products Table
- `product_id` (Primary Key)
- `product_name`
- `product_brand`
- `quantity`
- `price`
- `created_at`, `updated_at`

### Sales Table
- `sales_id` (Primary Key)
- `product_id` (Foreign Key)
- `quantity`
- `date`
- `created_at`, `updated_at`

### Purchases Table
- `purchase_id` (Primary Key)
- `supplier_id` (Foreign Key)
- `product_id` (Foreign Key)
- `user_id` (Foreign Key)
- `price`
- `quantity`
- `purchase_date`
- `created_at`, `updated_at`

### Returns Table
- `return_id` (Primary Key)
- `product_id` (Foreign Key)
- `quantity`
- `return_status`
- `price`
- `return_date`
- `created_at`, `updated_at`

## Usage Examples

### Basic Product Operations

```php
use App\Models\Product;

// Create a product
$product = Product::create([
    'product_name' => 'Laptop Computer',
    'product_brand' => 'TechBrand',
    'quantity' => 50,
    'price' => 999.99
]);

// Check stock
if ($product->isInStock(5)) {
    echo "Product has sufficient stock";
}

// Update stock
$product->decreaseStock(10); // After sale
$product->increaseStock(20); // After purchase
```

### Using ProductService

```php
use App\Services\ProductService;

$service = new ProductService();

// Process a sale
$result = $service->processSale($productId, 5);
if ($result['success']) {
    echo "Sale processed: " . $result['data']['total_amount'];
}

// Get dashboard data
$dashboard = $service->getInventoryDashboard();
echo "Total inventory value: " . $dashboard['total_inventory_value'];

// Search products
$products = $service->searchProducts([
    'search' => 'laptop',
    'min_price' => 500,
    'stock_status' => 'in_stock'
]);
```

### Working with Sales

```php
use App\Models\Sale;

// Get today's sales
$todaySales = Sale::today()->get();

// Get top selling products
$topProducts = Sale::topSellingProducts(10);

// Process a sale
$sale = Sale::processSale($productId, 5);
```

### Working with Returns

```php
use App\Models\Returns;

// Create a return
$return = Returns::processReturn($productId, 2, 999.99);

// Approve a return
if ($return->approve()) {
    echo "Return approved and stock updated";
}

// Get pending returns
$pending = Returns::pending()->get();
```

## Features Summary

✅ **Product Management**
- Full CRUD operations
- Stock level tracking
- Price management
- Brand and name organization

✅ **Sales Processing**
- Automatic inventory updates
- Sales analytics and reporting
- Date-based sales tracking
- Top selling products identification

✅ **Purchase Management**
- Supplier relationship tracking
- Automatic stock updates
- Price tracking and updates
- Purchase analytics

✅ **Returns Handling**
- Multi-status return workflow
- Automatic inventory adjustments
- Return analytics
- Return approval process

✅ **Inventory Management**
- Low stock alerts
- Out of stock tracking
- Inventory value calculations
- Stock movement history

✅ **Analytics & Reporting**
- Sales analytics by date range
- Profit margin calculations
- Stock turnover rates
- Product performance metrics

✅ **Search & Filtering**
- Advanced product search
- Multiple filter criteria
- Sortable results
- Status-based filtering

The Product module is now fully functional with comprehensive inventory management, sales tracking, purchase processing, and returns handling capabilities.

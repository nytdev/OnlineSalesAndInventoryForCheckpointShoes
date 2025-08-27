<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SalesOrderService
{
    /**
     * Get paginated sales orders with filters and search.
     */
    public function getPaginatedOrders(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = SalesOrder::with(['customer', 'items']);

        // Apply search
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Apply filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        if ($paymentStatus = $request->get('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Date range filters
        if ($startDate = $request->get('start_date')) {
            $query->where('order_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->where('order_date', '<=', $endDate);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortField, ['order_number', 'order_date', 'total_amount', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for sales order listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'customers' => Customer::active()
                                 ->orderBy('first_name')
                                 ->get()
                                 ->map(function ($customer) {
                                     return [
                                         'id' => $customer->customer_id,
                                         'name' => $customer->display_name,
                                     ];
                                 }),
            'products' => Product::orderBy('name')
                               ->get()
                               ->map(function ($product) {
                                   return [
                                       'id' => $product->product_id,
                                       'name' => $product->name,
                                       'price' => $product->price,
                                       'stock' => $product->quantity,
                                   ];
                               }),
        ];
    }

    /**
     * Create a new sales order.
     */
    public function createOrder(array $data): SalesOrder
    {
        // Set defaults
        $data['order_date'] = $data['order_date'] ?? Carbon::today();
        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 'normal';
        $data['payment_status'] = $data['payment_status'] ?? 'pending';

        // Create the order
        $order = SalesOrder::create($data);

        // Add items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->addItemsToOrder($order, $data['items']);
        }

        return $order->fresh(['customer', 'items.product']);
    }

    /**
     * Update a sales order.
     */
    public function updateOrder(SalesOrder $order, array $data): SalesOrder
    {
        // Update order details
        $order->update($data);

        // Update items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->updateOrderItems($order, $data['items']);
        }

        return $order->fresh(['customer', 'items.product']);
    }

    /**
     * Add items to an order.
     */
    public function addItemsToOrder(SalesOrder $order, array $items): void
    {
        foreach ($items as $itemData) {
            $product = Product::find($itemData['product_id']);
            
            if (!$product) {
                continue;
            }

            // Use product price if unit price not provided
            $unitPrice = $itemData['unit_price'] ?? $product->price;
            $quantity = $itemData['quantity'];
            $discountAmount = $itemData['discount_amount'] ?? 0;

            SalesOrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_amount' => $discountAmount,
                'line_total' => ($quantity * $unitPrice) - $discountAmount,
                'notes' => $itemData['notes'] ?? null,
            ]);
        }

        // Recalculate order totals
        $order->calculateTotals();
    }

    /**
     * Update order items.
     */
    public function updateOrderItems(SalesOrder $order, array $items): void
    {
        // Delete existing items
        $order->items()->delete();

        // Add new items
        $this->addItemsToOrder($order, $items);
    }

    /**
     * Delete a sales order.
     */
    public function deleteOrder(SalesOrder $order): bool
    {
        // Check if order can be deleted
        if (!$order->canBeEdited()) {
            throw new \Exception('Cannot delete order that is already processed.');
        }

        // Delete order items first (cascade should handle this, but being explicit)
        $order->items()->delete();

        return $order->delete();
    }

    /**
     * Change order status.
     */
    public function changeOrderStatus(SalesOrder $order, string $status): SalesOrder
    {
        $validTransitions = $this->getValidStatusTransitions($order->status);
        
        if (!in_array($status, $validTransitions)) {
            throw new \Exception("Cannot change status from {$order->status} to {$status}");
        }

        $order->update(['status' => $status]);

        // Update shipped date when status changes to shipped
        if ($status === 'shipped' && !$order->shipped_date) {
            $order->update(['shipped_date' => Carbon::now()]);
        }

        return $order->fresh();
    }

    /**
     * Get valid status transitions for current status.
     */
    public function getValidStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'returned'],
            'delivered' => ['returned'],
            'cancelled' => [],
            'returned' => [],
            default => [],
        };
    }

    /**
     * Get sales order analytics data.
     */
    public function getOrderAnalytics(): array
    {
        $totalOrders = SalesOrder::count();
        $pendingOrders = SalesOrder::pending()->count();
        $confirmedOrders = SalesOrder::confirmed()->count();
        $processingOrders = SalesOrder::processing()->count();
        $shippedOrders = SalesOrder::shipped()->count();
        $deliveredOrders = SalesOrder::delivered()->count();
        $cancelledOrders = SalesOrder::cancelled()->count();

        // Recent orders (last 30 days)
        $recentOrders = SalesOrder::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Overdue orders
        $overdueOrders = SalesOrder::overdue();

        // Revenue analytics
        $totalRevenue = SalesOrder::whereIn('status', ['delivered', 'shipped'])->sum('total_amount');
        $monthlyRevenue = SalesOrder::whereIn('status', ['delivered', 'shipped'])
                                   ->thisMonth()
                                   ->sum('total_amount');
        $todayRevenue = SalesOrder::whereIn('status', ['delivered', 'shipped'])
                                 ->today()
                                 ->sum('total_amount');

        // Order trend (last 12 months)
        $orderTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = SalesOrder::whereYear('created_at', $date->year)
                              ->whereMonth('created_at', $date->month)
                              ->count();
            $revenue = SalesOrder::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->whereIn('status', ['delivered', 'shipped'])
                                ->sum('total_amount');
            
            $orderTrend->push([
                'month' => $date->format('M Y'),
                'orders' => $count,
                'revenue' => $revenue
            ]);
        }

        // Top customers by order value
        $topCustomers = SalesOrder::with(['customer'])
                                 ->selectRaw('customer_id, COUNT(*) as order_count, SUM(total_amount) as total_spent')
                                 ->whereIn('status', ['delivered', 'shipped'])
                                 ->groupBy('customer_id')
                                 ->orderByDesc('total_spent')
                                 ->limit(10)
                                 ->get();

        // Average order value
        $avgOrderValue = SalesOrder::whereIn('status', ['delivered', 'shipped'])
                                  ->avg('total_amount') ?? 0;

        return [
            'summary' => [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'confirmed_orders' => $confirmedOrders,
                'processing_orders' => $processingOrders,
                'shipped_orders' => $shippedOrders,
                'delivered_orders' => $deliveredOrders,
                'cancelled_orders' => $cancelledOrders,
                'recent_orders' => $recentOrders,
                'overdue_orders' => $overdueOrders->count(),
                'total_revenue' => $totalRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'today_revenue' => $todayRevenue,
                'avg_order_value' => round($avgOrderValue, 2),
            ],
            'order_trend' => $orderTrend,
            'top_customers' => $topCustomers,
            'overdue_orders' => $overdueOrders,
        ];
    }

    /**
     * Get order history for a customer.
     */
    public function getCustomerOrderHistory(Customer $customer, int $perPage = 15): LengthAwarePaginator
    {
        return $customer->salesOrders()
                       ->with(['items.product'])
                       ->latest('created_at')
                       ->paginate($perPage);
    }

    /**
     * Check stock availability for order items.
     */
    public function checkStockAvailability(array $items): array
    {
        $stockIssues = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                $stockIssues[] = [
                    'product_id' => $item['product_id'],
                    'issue' => 'Product not found',
                ];
                continue;
            }

            $requestedQty = $item['quantity'];
            $availableQty = $product->quantity;

            if ($requestedQty > $availableQty) {
                $stockIssues[] = [
                    'product_id' => $product->product_id,
                    'product_name' => $product->name,
                    'requested' => $requestedQty,
                    'available' => $availableQty,
                    'shortage' => $requestedQty - $availableQty,
                    'issue' => 'Insufficient stock',
                ];
            }
        }

        return $stockIssues;
    }

    /**
     * Get orders requiring attention.
     */
    public function getOrdersRequiringAttention(): Collection
    {
        return SalesOrder::requiresAttention()
                        ->with(['customer'])
                        ->get();
    }

    /**
     * Process order fulfillment (reduce inventory).
     */
    public function processOrderFulfillment(SalesOrder $order): bool
    {
        if ($order->status !== 'confirmed') {
            throw new \Exception('Only confirmed orders can be processed for fulfillment.');
        }

        // Check stock availability for all items
        $stockIssues = [];
        foreach ($order->items as $item) {
            if (!$item->hasSufficientStock()) {
                $stockIssues[] = $item;
            }
        }

        if (!empty($stockIssues)) {
            throw new \Exception('Insufficient stock for some items in this order.');
        }

        // Reduce inventory
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->quantity -= $item->quantity;
            $product->save();

            // Create stock movement record if the model exists
            if (class_exists('App\Models\StockMovement')) {
                \App\Models\StockMovement::create([
                    'product_id' => $product->product_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'reference_type' => 'sales_order',
                    'reference_id' => $order->order_id,
                    'notes' => "Order fulfillment for order {$order->order_number}",
                    'date' => Carbon::now(),
                ]);
            }
        }

        // Update order status
        $order->update(['status' => 'processing']);

        return true;
    }

    /**
     * Calculate order summary.
     */
    public function calculateOrderSummary(array $items, float $taxRate = 0, float $shippingAmount = 0, float $discountAmount = 0): array
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $lineTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0);
            $subtotal += $lineTotal;
        }

        $taxAmount = $subtotal * ($taxRate / 100);
        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'shipping_amount' => round($shippingAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }
}

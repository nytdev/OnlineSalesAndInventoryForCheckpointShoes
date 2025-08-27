<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SalesOrderService;

class SalesOrderController extends Controller
{
    protected SalesOrderService $orderService;

    public function __construct(SalesOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getPaginatedOrders($request);
        $filterOptions = $this->orderService->getFilterOptions();

        return view('sales.orders.index', [
            'orders' => $orders,
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filterOptions = $this->orderService->getFilterOptions();
        return view('sales.orders.create', [
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,customer_id',
            'order_date' => 'required|date',
            'required_date' => 'nullable|date|after_or_equal:order_date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,check',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',

            'shipping_address' => 'nullable|string|max:2000',
            'billing_address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Optionally check stock availability before creating order
        $stockIssues = $this->orderService->checkStockAvailability($data['items']);
        if (!empty($stockIssues)) {
            return redirect()->back()->withErrors(['items' => 'Insufficient stock for some items.'])->withInput();
        }

        $order = $this->orderService->createOrder($data);

        return redirect()->route('sales.orders.show', $order->order_id)
            ->with('success', 'Sales order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesOrder $order)
    {
        $order->load(['customer', 'items.product']);
        return view('sales.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesOrder $order)
    {
        $order->load(['customer', 'items.product']);
        $filterOptions = $this->orderService->getFilterOptions();
        return view('sales.orders.edit', [
            'order' => $order,
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesOrder $order)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,customer_id',
            'order_date' => 'required|date',
            'required_date' => 'nullable|date|after_or_equal:order_date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,check',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',

            'shipping_address' => 'nullable|string|max:2000',
            'billing_address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Optionally check stock availability before updating
        $stockIssues = $this->orderService->checkStockAvailability($data['items']);
        if (!empty($stockIssues)) {
            return redirect()->back()->withErrors(['items' => 'Insufficient stock for some items.'])->withInput();
        }

        $this->orderService->updateOrder($order, $data);

        return redirect()->route('sales.orders.show', $order->order_id)
            ->with('success', 'Sales order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesOrder $order)
    {
        try {
            $this->orderService->deleteOrder($order);
            return redirect()->route('sales.orders.index')
                ->with('success', 'Sales order deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('sales.orders.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Change order status
     */
    public function changeStatus(Request $request, SalesOrder $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->orderService->changeOrderStatus($order, $request->get('status'));
            return redirect()->back()->with('success', 'Order status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fulfill order (reduce inventory)
     */
    public function fulfill(SalesOrder $order)
    {
        try {
            $this->orderService->processOrderFulfillment($order);
            return redirect()->back()->with('success', 'Order fulfillment started successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Analytics
     */
    public function analytics()
    {
        $analytics = $this->orderService->getOrderAnalytics();
        return response()->json($analytics);
    }
}

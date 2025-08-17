<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Sale extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'sales_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'quantity',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the sale.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Scope a query to only include sales from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('date', Carbon::today());
    }

    /**
     * Scope a query to only include sales from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to only include sales from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year);
    }

    /**
     * Scope a query to only include sales from a date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get the total sale amount (quantity * product price).
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->quantity * $this->product->price;
    }

    /**
     * Process a new sale and update product inventory.
     */
    public static function processSale(int $productId, int $quantity, $date = null): ?self
    {
        $product = Product::find($productId);
        
        if (!$product || !$product->isInStock($quantity)) {
            return null;
        }

        // Create the sale record
        $sale = self::create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'date' => $date ?? now(),
        ]);

        // Update product inventory
        $product->decreaseStock($quantity);

        return $sale;
    }

    /**
     * Calculate total sales amount for a given period.
     */
    public static function totalSalesAmount($startDate = null, $endDate = null): float
    {
        $query = self::with('product');
        
        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }
        
        return $query->get()->sum('total_amount');
    }

    /**
     * Get top selling products.
     */
    public static function topSellingProducts(int $limit = 10)
    {
        return self::selectRaw('product_id, SUM(quantity) as total_sold')
                   ->with('product')
                   ->groupBy('product_id')
                   ->orderByDesc('total_sold')
                   ->limit($limit)
                   ->get();
    }
}

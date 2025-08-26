<?php

namespace App\Imports;

use App\Models\Product;
use App\Services\StockService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class StockImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected StockService $stockService;
    protected int $rowCount = 0;
    protected array $errors = [];

    public function __construct()
    {
        $this->stockService = new StockService();
    }

    /**
     * @param array $row
     */
    public function model(array $row)
    {
        $this->rowCount++;

        try {
            // Validate that product exists
            $product = Product::find($row['product_id']);
            if (!$product) {
                $this->errors[] = "Row {$this->rowCount}: Product ID {$row['product_id']} not found.";
                return null;
            }

            // Prepare data for stock adjustment
            $adjustmentData = [
                'product_id' => $row['product_id'],
                'new_quantity' => (int) $row['new_quantity'],
                'unit_cost' => !empty($row['unit_cost']) ? (float) $row['unit_cost'] : null,
                'reason' => $row['reason'] ?? 'Bulk import adjustment',
                'notes' => $row['notes'] ?? null,
                'movement_date' => now(),
            ];

            // Create stock adjustment via service
            $result = $this->stockService->createStockAdjustment($adjustmentData);

            if (!$result['success']) {
                $this->errors[] = "Row {$this->rowCount}: " . $result['message'];
            }

        } catch (\Exception $e) {
            $this->errors[] = "Row {$this->rowCount}: " . $e->getMessage();
        }

        // Return null since we handle creation through the service
        return null;
    }

    /**
     * Validation rules for each row.
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,product_id',
            'new_quantity' => 'required|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function customValidationMessages()
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.integer' => 'Product ID must be a valid number.',
            'product_id.exists' => 'Product ID does not exist in the system.',
            'new_quantity.required' => 'New quantity is required.',
            'new_quantity.integer' => 'New quantity must be a valid number.',
            'new_quantity.min' => 'New quantity cannot be negative.',
            'unit_cost.numeric' => 'Unit cost must be a valid number.',
            'unit_cost.min' => 'Unit cost cannot be negative.',
            'reason.string' => 'Reason must be text.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
            'notes.string' => 'Notes must be text.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get the number of rows processed.
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * Get any errors that occurred during import.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if import had any errors.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors) || !empty($this->failures());
    }

    /**
     * Get all errors including validation failures.
     */
    public function getAllErrors(): array
    {
        $allErrors = $this->errors;
        
        foreach ($this->failures() as $failure) {
            $allErrors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        
        return $allErrors;
    }
}

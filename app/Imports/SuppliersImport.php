<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;

class SuppliersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    private $rowCount = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->rowCount++;

        return new Supplier([
            'supplier_name' => $row['supplier_name'] ?? $row['name'],
            'supplier_contact' => $row['supplier_contact'] ?? $row['contact'] ?? $row['phone'],
            'email' => $row['email'],
            'type' => $row['type'] ?? 'local',
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'postal_code' => $row['postal_code'] ?? $row['zip'] ?? null,
            'country' => $row['country'] ?? 'Philippines',
            'status' => $row['status'] ?? 'active',
            'tax_id' => $row['tax_id'] ?? $row['taxid'] ?? null,
            'payment_terms' => $row['payment_terms'] ?? $row['terms'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'supplier_name' => 'required|string|max:255',
            'name' => 'required_without:supplier_name|string|max:255',
            'supplier_contact' => 'required_without_all:contact,phone|string|max:20',
            'contact' => 'required_without_all:supplier_contact,phone|string|max:20',
            'phone' => 'required_without_all:supplier_contact,contact|string|max:20',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('suppliers', 'email')
            ],
            'type' => 'nullable|string|in:local,international,distributor,manufacturer,service_provider',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
            'tax_id' => 'nullable|string|max:50',
            'taxid' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'supplier_name.required' => 'The supplier name field is required.',
            'name.required_without' => 'Either supplier_name or name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email address is already registered for another supplier.',
            'type.in' => 'The supplier type must be one of: local, international, distributor, manufacturer, service_provider.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle validation failures
        // You could log these, send notifications, etc.
        foreach ($failures as $failure) {
            logger()->error('Supplier import validation failed', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ]);
        }
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}

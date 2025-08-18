<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CustomersImport
{
    private $rowCount = 0;

    public function __construct()
    {
        $this->rowCount = 0;
    }

    public function import($file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        
        if ($handle === false) {
            throw new \Exception('Could not open file for reading');
        }

        $header = fgetcsv($handle); // Skip header row
        $this->rowCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) { // Ensure minimum required columns (first_name, last_name, email)
                try {
                    // Map CSV columns to database fields
                    $data = [
                        'first_name' => $row[0] ?? '',
                        'last_name' => $row[1] ?? '',
                        'email' => $row[2] ?? '',
                        'phone' => $row[3] ?? null,
                        'address' => $row[4] ?? null,
                        'city' => $row[5] ?? null,
                        'state' => $row[6] ?? null,
                        'postal_code' => $row[7] ?? null,
                        'country' => $row[8] ?? 'Philippines',
                        'date_of_birth' => $this->parseDate($row[9] ?? null),
                        'customer_type' => in_array(strtolower($row[10] ?? 'individual'), ['individual', 'business']) 
                            ? strtolower($row[10]) : 'individual',
                        'company_name' => $row[11] ?? null,
                        'tax_id' => $row[12] ?? null,
                        'notes' => $row[13] ?? null,
                        'status' => 'active', // Default to active
                    ];

                    // Validate the data
                    $validator = Validator::make($data, [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email|unique:customers,email|max:255',
                        'phone' => 'nullable|string|max:20',
                        'address' => 'nullable|string|max:1000',
                        'city' => 'nullable|string|max:255',
                        'state' => 'nullable|string|max:255',
                        'postal_code' => 'nullable|string|max:20',
                        'country' => 'nullable|string|max:255',
                        'date_of_birth' => 'nullable|date|before:today',
                        'customer_type' => 'required|in:individual,business',
                        'company_name' => 'nullable|string|max:255',
                        'tax_id' => 'nullable|string|max:50',
                        'notes' => 'nullable|string|max:2000',
                    ]);

                    if ($validator->passes()) {
                        // Remove null values to avoid database issues
                        $cleanData = array_filter($data, function($value) {
                            return $value !== null && $value !== '';
                        });

                        // Ensure required fields are present
                        $cleanData['first_name'] = $data['first_name'];
                        $cleanData['last_name'] = $data['last_name'];
                        $cleanData['email'] = $data['email'];
                        $cleanData['customer_type'] = $data['customer_type'];
                        $cleanData['status'] = $data['status'];

                        Customer::create($cleanData);
                        $this->rowCount++;
                    } else {
                        Log::warning('Customer import validation failed', [
                            'row' => $row,
                            'errors' => $validator->errors()->toArray()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error importing customer: ' . $e->getMessage(), $row);
                    // Continue with next row instead of failing completely
                }
            }
        }

        fclose($handle);
        return $this;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    private function parseDate($dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'Y-m-d H:i:s', 'm-d-Y'];
            
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }
            
            // Try Carbon's flexible parsing
            $date = Carbon::parse($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Could not parse date: ' . $dateString);
            return null;
        }
    }
}

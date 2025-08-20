<?php

namespace App\Http\Requests;

use App\Models\Returns;
use Illuminate\Foundation\Http\FormRequest;

class StoreReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'exists:products,product_id',
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999999',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'return_status' => [
                'required',
                'string',
                'in:' . implode(',', Returns::getStatuses()),
            ],
            'return_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . now()->subYear()->toDateString(), // Max 1 year ago
            ],
            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product for the return.',
            'product_id.exists' => 'The selected product does not exist.',
            'quantity.required' => 'Please specify the quantity being returned.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 999,999.',
            'price.required' => 'Please specify the unit price.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'price.max' => 'Price cannot exceed $999,999.99.',
            'return_status.required' => 'Please select a return status.',
            'return_status.in' => 'The selected return status is invalid.',
            'return_date.required' => 'Please specify the return date.',
            'return_date.date' => 'Return date must be a valid date.',
            'return_date.before_or_equal' => 'Return date cannot be in the future.',
            'return_date.after_or_equal' => 'Return date cannot be more than one year ago.',
            'reason.max' => 'Return reason cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'product',
            'return_status' => 'status',
            'return_date' => 'return date',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic can be added here
            // For example, checking if the product has enough stock for returns
            // or business-specific validation rules
        });
    }
}

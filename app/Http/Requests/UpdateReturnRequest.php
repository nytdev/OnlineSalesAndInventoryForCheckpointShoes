<?php

namespace App\Http\Requests;

use App\Models\Returns;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow updating pending returns
        $return = $this->route('return');
        return auth()->check() && $return && $return->isPending();
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
            $return = $this->route('return');
            
            // Ensure we can only update pending returns
            if ($return && !$return->isPending()) {
                $validator->errors()->add('return_status', 'Only pending returns can be updated.');
            }
        });
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'You are not authorized to update this return. Only pending returns can be edited.'
        );
    }
}

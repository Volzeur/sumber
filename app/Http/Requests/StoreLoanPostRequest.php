<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'amount_needed' => ['required', 'numeric', 'min:10000', 'max:999999999'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'collateral_description' => ['required', 'string', 'max:500'],
            'repayment_period' => ['required', 'integer', 'min:1', 'max:365'],
            'repayment_unit' => ['required', 'in:days,weeks,months,years'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount_needed.min' => 'Minimum loan amount is Rp 10,000.',
            'interest_rate.max' => 'Interest rate cannot exceed 100%.',
        ];
    }
}
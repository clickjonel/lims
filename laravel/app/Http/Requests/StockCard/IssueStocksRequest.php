<?php

namespace App\Http\Requests\StockCard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class IssueStocksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'stock_card_id' => 'required|numeric',
        'transaction_date' => 'required|date',
        'quantity' => 'required|numeric',
        'recepient' => 'required|string',
        'ptr_no' => 'required|string',
        'remarks' => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests\StockCard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateItemStockCardRequest extends FormRequest
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
            'stock_no' => 'required|string',
            'stock_name' => 'required|string',
            'contract_no' => 'required|string',
            'entity_name' => 'required|string',
            'iar_no' => 'required|string',
            'iar_date' => 'required|string',
            'supplier_name' => 'required|string',
            'supplier_address' => 'required|string',
            'item_description' => 'required|string',
            'dosage_form' => 'nullable|string',
            'dosage_strength' => 'nullable|string',
            'measurement_unit' => 'required|numeric',
            'unit_cost' => 'required|numeric',
            'procurement_mode' => 'required|numeric',
            'fund_cluster' => 'required|numeric',
            'warehouse' => 'required|numeric',
            'batch_no' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'category' => 'required|numeric',
            'req_office' => 'required|numeric',
            'quantity' => 'required|numeric',
            'item_id' => 'required|numeric'
        ];
    }
}

<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
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
            'id' => 'required|numeric',
            'entity_name' => 'required|string',
            'fund_source' => 'required|numeric',
            'source_name' => 'required|string',
            'source_address' => 'required|string',
            'iar_no' => 'required|string',
            'iar_date' => 'required|string',
            'po_no' => 'nullable|string',
            'po_date' => 'nullable|date',
            'ptr_no' => 'nullable|string',
            'ptr_date' => 'nullable|date',
            'bl_no' => 'nullable|string',
            'bl_date' => 'nullable|date',
            'dnf_no' => 'nullable|string',
            'dnf_date' => 'nullable|date',
            'req_office' => 'required|numeric',
            'end_user' => 'required|numeric',
            'payment_term' => 'required|numeric',
            'completion' => 'required|numeric',
            'purpose' => 'required|string',
            'invoices' => 'nullable|array',
            'receipts' => 'required|array',
            'items' => 'required|array'
        ];
    }
}

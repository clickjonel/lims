<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockCard\CreateItemStockCardRequest;
use App\Http\Requests\StockCard\CreateStockCardRequest;
use App\Http\Requests\StockCard\IssueStocksRequest;
use App\Http\Requests\StockCard\UpdateStockCardRequest;
use App\Models\Delivery;
use App\Models\DeliveryItems;
use App\Models\StockCard;
use App\Models\StockCardTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StockCardController extends Controller
{
    public function list(Request $request):JsonResponse
    {
        $page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;
        $searchKeyword = trim($request->searchKeyword ?? '');
        $section_id = $request->section_id ?? null;
        $category_id = $request->category_id ?? null;

        $stockCardEloquent = StockCard::with([
                    'stockCardTransactions',
                    'latestTransaction',
                    'section'
                    ])
                    ->when($section_id, function ($query) use ($section_id) {
                        $query->where('req_office', $section_id);
                    })
                    ->when($category_id, function ($query) use ($category_id) {
                        $query->where('category', $category_id);
                    })
                    ->when($searchKeyword, function ($query) use ($searchKeyword) {
                        $query->where('stock_no', 'like', '%' . $searchKeyword . '%')
                                ->orWhere('item_description', 'like', '%' . $searchKeyword . '%');
                    })
                    ->orderBy('id','DESC')
                    ->withCount('stockCardTransactions');

        $total = $stockCardEloquent->count();  
        $stock_cards = $stockCardEloquent->offset(($page - 1) * $perPage)->limit($perPage)->get();  

        return response()->json([
            'list' => $stock_cards,
            'total' => $total
        ]);
    }

    public function create(CreateStockCardRequest $request):JsonResponse
    {
        $validated = $request->validated();

        $stockCard = StockCard::create($validated);

        StockCardTransaction::create([
            'stock_card_id' => $stockCard->id,
            'transaction_date' => $validated['received_date'],
            'received' => $validated['quantity'],
            'issued' => null,
            'balance' => $validated['quantity'],
            'total_cost' => $validated['unit_cost'] * $validated['quantity'],
            'ptr_no' => null,
            'iar_no' => $validated['iar_no'],
            'recepient' => 'DOH-CHD-CAR',
            'remarks' => null
        ]);

        return response()->json([
            'message' => 'Stock Card Created Successfully'
        ]);

    }

    public function find(Request $request):JsonResponse
    {
        $stockCard = StockCard::with(['stockCardTransactions','section'])->find($request->id);

        return response()->json([
            'stock_card' => $stockCard
        ]);
    }

    public function update(UpdateStockCardRequest $request):JsonResponse
    {
        $validated = $request->validated();

        $stockCard = StockCard::find($validated['id']);
        $stockCard->update($validated);
       
        foreach($validated['stock_card_transactions'] as $transaction){
            StockCardTransaction::find($transaction['id'])->update([
                'transaction_date' => $transaction['transaction_date'],
                'received' => $transaction['received'],
                'issued' => $transaction['issued'],
                'balance' => $transaction['balance'],
                'total_cost' => $transaction['total_cost'],
                'ptr_no' => $transaction['ptr_no'],
                'iar_no' => $transaction['iar_no'],
                'recepient' => $transaction['recepient'],
                'remarks' => $transaction['remarks']
            ]);
        }

        return response()->json([
            'message' => 'Stock Card Updated Successfully'
        ]);
    }

    public function issue(IssueStocksRequest $request):JsonResponse
    {
        $validated = $request->validated();

        $stockCard = StockCard::find($validated['stock_card_id']);
        $latestTransaction = $stockCard->latestTransaction;

        StockCardTransaction::create([
                'stock_card_id' => $validated['stock_card_id'],
                'transaction_date' => $validated['transaction_date'],
                'received' => null,
                'issued' => $validated['quantity'],
                'balance' => $latestTransaction->balance - $validated['quantity'],
                'total_cost' => $validated['quantity'] * $stockCard->unit_cost,
                'ptr_no' => $validated['ptr_no'],
                'iar_no' => null,
                'recepient' => $validated['recepient'],
                'remarks' => $validated['remarks'] ?? null
        ]);

        return response()->json([
            'message' => 'Stock Issued Successfully Successfully'
        ]);

    }

    public function createItemStockCard(CreateItemStockCardRequest $request):JsonResponse
    {
        $validated = $request->validated();

        $stockCard = StockCard::create($validated);

        StockCardTransaction::create([
            'stock_card_id' => $stockCard->id,
            'transaction_date' => $validated['iar_date'],
            'received' => $validated['quantity'],
            'issued' => null,
            'balance' => $validated['quantity'],
            'total_cost' => $validated['unit_cost'] * $validated['quantity'],
            'ptr_no' => null,
            'iar_no' => $validated['iar_no'],
            'recepient' => 'DOH-CHD-CAR',
            'remarks' => null
        ]);

        DeliveryItems::find($validated['item_id'])->update(['stocked'=>1]);

        return response()->json([
            'message' => 'Stock Card has been Created for this Item',
            // 'item' => $request->all()
        ]); 
    }




}
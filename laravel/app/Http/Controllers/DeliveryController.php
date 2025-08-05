<?php

namespace App\Http\Controllers;

use App\Http\Requests\Delivery\CreateRequest;
use App\Http\Requests\Delivery\UpdateRequest;
use App\Models\Delivery;
use App\Models\DeliveryInvoices;
use App\Models\DeliveryItems;
use App\Models\DeliveryReceipts;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;

class DeliveryController extends Controller
{
    public function list(Request $request):JsonResponse
    {
        $page = $request->page ?? 1;
        $perPage = $request->perPage ?? 10;
        $searchKeyword = trim($request->searchKeyword ?? '');
        $user_id = $request->user_id ?? null;
        $section_id = $request->section_id ?? null;

        $deliveryEloquent = Delivery::with(['fundSource','requisitioningSection','endUser','deliveryReceipts','deliveryInvoices'])
                    ->when($user_id, function ($query) use ($user_id) {
                        $query->where('end_user', $user_id);
                    })
                    ->when($section_id, function ($query) use ($section_id) {
                        $query->where('req_office', $section_id);
                    })
                    ->when($searchKeyword, function ($query) use ($searchKeyword) {
                        $query->where('iar_no', 'like', '%' . $searchKeyword . '%');
                    })
                    ->orderBy('id','DESC')->withCount(['deliveryReceipts','deliveryInvoices']);

        $total = $deliveryEloquent->count();  
        $deliveries = $deliveryEloquent->offset(($page - 1) * $perPage)->limit($perPage)->get();  

        return response()->json([
            'list' => $deliveries,
            'total' => $total
        ]);
    }

    public function create(CreateRequest $request):JsonResponse
    {
        DB::transaction(function() use ($request){
            $validated = $request->validated();

            $delivery = Delivery::create([
                'hashid' => sha1($validated['iar_no']),
                'entity_name' => $validated['entity_name'],
                'fund_source' => $validated['fund_source'],
                'source_name' => $validated['source_name'],
                'source_address' => $validated['source_address'],
                'iar_no' => $validated['iar_no'],
                'iar_date' => $validated['iar_date'],
                'po_no' => $validated['po_no'],
                'po_date' => $validated['po_date'],
                'ptr_no' => $validated['ptr_no'],
                'ptr_date' => $validated['ptr_date'],
                'bl_no' => $validated['bl_no'],
                'bl_date' => $validated['bl_date'],
                'dnf_no' => $validated['dnf_no'],
                'dnf_date' => $validated['dnf_date'],
                'req_office' => $validated['req_office'],
                'end_user' => $validated['end_user'],
                'payment_term' => $validated['payment_term'],
                'completion' => $validated['completion'],
                'purpose' => $validated['purpose'],
            ]);

            if($validated['invoices']){
                $delivery->deliveryInvoices()->createMany($validated['invoices']);
            }
            $delivery->deliveryReceipts()->createMany($validated['receipts']);
            $delivery->deliveryItems()->createMany($validated['items']);

            DB::commit();
        });

        return response()->json([
            'message' => 'Delivery Successfully Created'
        ]);
    }

    public function find(Request $request):JsonResponse 
    {
        $delivery = Delivery::with(['deliveryInvoices','deliveryReceipts','deliveryItems'])->find($request->id);

        return response()->json([
            'delivery' => $delivery
        ]);
    }

    public function update(UpdateRequest $request):JsonResponse
    {

        DB::transaction(function() use ($request){
            $validated = $request->validated();

            $delivery = Delivery::find($validated['id']);
            $delivery->update([
                'entity_name' => $validated['entity_name'],
                'fund_source' => $validated['fund_source'],
                'source_name' => $validated['source_name'],
                'source_address' => $validated['source_address'],
                'iar_no' => $validated['iar_no'],
                'iar_date' => $validated['iar_date'],
                'po_no' => $validated['po_no'],
                'po_date' => $validated['po_date'],
                'ptr_no' => $validated['ptr_no'],
                'ptr_date' => $validated['ptr_date'],
                'bl_no' => $validated['bl_no'],
                'bl_date' => $validated['bl_date'],
                'dnf_no' => $validated['dnf_no'],
                'dnf_date' => $validated['dnf_date'],
                'req_office' => $validated['req_office'],
                'end_user' => $validated['end_user'],
                'payment_term' => $validated['payment_term'],
                'completion' => $validated['completion'],
                'purpose' => $validated['purpose'],
            ]);

            if($validated['invoices']){
               foreach($validated['invoices'] as $invoice){
                   if(!empty($invoice['id'])){
                        DeliveryInvoices::find($invoice['id'])->update($invoice);
                   }
                   else{
                        $delivery->deliveryInvoices()->create($invoice);
                   }
               }
            }

            foreach($validated['receipts'] as $receipt){
                if(!empty($receipt['id'])){
                    DeliveryReceipts::find($receipt['id'])->update($receipt);
                }
                else{
                    $delivery->deliveryReceipts()->create($receipt);
                }
            }

            foreach($validated['items'] as $item){
                if(!empty($item['id'])){
                    DeliveryItems::find($item['id'])->update($item);
                }
                else{
                    $delivery->deliveryItems()->create($item);
                }
            }

            DB::commit();
        });

        return response()->json([
            'message' => 'Delivery Successfully Updated'
        ]);
    }

    public function getIARData(Request $request):JsonResponse
    {
       $delivery = Delivery::with([
            'fundSource',
            'requisitioningSection',
            'deliveryItems.measurementUnit', 
            'delivered_items', 
            'balance_items',
            'deliveryInvoices',
            'deliveryReceipts',
            'endUser.assignment.item'
        ])

        //sum of unit cost
        ->withSum('deliveryItems as total_sum_items', 'unit_cost') 
        ->withSum('delivered_items as total_sum_delivered', 'unit_cost') 
        ->withSum('balance_items as total_sum_balance', 'unit_cost') 

        //total quantity
        ->withSum('deliveryItems as total_quantity_items', 'quantity') 
        ->withSum('delivered_items as total_quantity_delivered', 'quantity') 
        ->withSum('balance_items as total_quantity_balance', 'quantity') 

        ->find($request->id);
        return response()->json([
            'delivery' => $delivery
        ]);
    }

    public function nodFindIAR(Request $request):JsonResponse
    {
        $delivery = Delivery::where('iar_no',$request->iar_no)->first();

       if($delivery){
            $items = $delivery->deliveryItems->where('availability',1)->load([
                'delivery.deliveryReceipts',
                'measurementUnit',
            ]);

            return response()->json([
                'status' => true,
                'items' => $items,
                'message' => 'IAR Items fetched successfully'
            ]);
       }
       else{
            return response()->json([
                'status' => false,
                'message' => 'IAR No not found'
            ]);
       }
    }

}

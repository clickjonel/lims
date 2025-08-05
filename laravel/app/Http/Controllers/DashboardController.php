<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryReceipts;
use App\Models\Property;
use App\Models\StockCard;
use App\Models\StockCardTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getDashboardData(Request $request):JsonResponse
    {
        $data = is_null($request->user_id) ? $this->getAdminDashboardData() : $this->getUserDashboardData($request->user_id);

        return response()->json($data);
    }

    public function getUserDashboardData($user_id): array
    {
        $user = User::with(['assignment'])->find($user_id);

        $recentDeliveryReceipts = DeliveryReceipts::with(['delivery.deliveryItems','delivery.requisitioningSection','delivery.endUser'])
                                    ->orderBy('delivery_date','DESC')
                                    ->whereHas('delivery',function($query) use ($user){
                                        $query->where('end_user',$user->user_id);
                                    });
        $recentStockTransactions = StockCardTransaction::with(['stockCard'])
                                    ->orderBy('transaction_date','DESC')
                                    ->whereHas('stockCard',function($query) use ($user){
                                        $query->where('req_office',$user->assignment->section_id);
                                    });
        $recentProperties = Property::with([ 'currentUser.user','firstUser'])
                            ->orderBy('id','DESC')
                            ->whereHas('currentUser',function($query) use ($user){
                                $query->where('user_id',$user->user_id);
                            })->limit(10);

        $totals = [
            'deliveries' => $recentDeliveryReceipts->count(),
            'stocks' => $recentStockTransactions->count(),
            'properties' => $recentProperties->count()
        ];

        $recents = [
            'receipts' => $recentDeliveryReceipts->get(),
            'transactions' => $recentStockTransactions->get(),
            'properties' => $recentProperties->get()
        ];


        return [
            'recents' => $recents,
            'totals' => $totals
        ];
    }

    public function getAdminDashboardData(): array
    {

        $recentDeliveryReceipts = DeliveryReceipts::with(['delivery.deliveryItems','delivery.requisitioningSection','delivery.endUser'])->orderBy('delivery_date','DESC')->limit(10);
        $recentStockTransactions = StockCardTransaction::with(['stockCard'])->orderBy('transaction_date','DESC')->limit(10);
        $recentProperties = Property::with([ 'currentUser.user','firstUser'])->orderBy('id','DESC')->limit(10);

        $totals = [
            'deliveries' => $recentDeliveryReceipts->count(),
            'stocks' => $recentStockTransactions->count(),
            'properties' => $recentProperties->count()
        ];

        $recents = [
            'receipts' => $recentDeliveryReceipts->get(),
            'transactions' => $recentStockTransactions->get(),
            'properties' => $recentProperties->get()
        ];


        return [
            'recents' => $recents,
            'totals' => $totals
        ];
    }

}

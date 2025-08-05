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
        $user = User::with(['assignment'])->find($request->user_id);

        $recents = [
            'receipts' => $this->recentDeliveryReceipts(),
            'transactions' => $this->recentStockTransactions(),
            'properties' => $this->fetchRecentProperties()
        ];

        $totals = [
            'deliveries' => Delivery::count(),
            'stocks' => StockCard::count(),
            'properties' => Property::count()
        ];

        return response()->json([
            'recents' => $recents,
            'totals' => $totals
        ]);
    }

    public function fetchRecentProperties()
    {
        $properties = Property::with([ 'currentUser.user','firstUser'])->orderBy('id','DESC')->limit(10)->get();

        return $properties;
    }

    public function recentStockTransactions()
    {
        $transactions = StockCardTransaction::with(['stockCard'])->orderBy('transaction_date','DESC')->limit(20)->get();

        return $transactions;
    }

    public function recentDeliveryReceipts()
    {
        $receipts = DeliveryReceipts::with(['delivery.deliveryItems','delivery.requisitioningSection','delivery.endUser'])->latest('id')->take(10)->get();

        return $receipts;
    }

    public function getDashboardCounts()
    {

    }

}

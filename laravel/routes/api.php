<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\FundSourceController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StockCardCategoryController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Models\Delivery;
use App\Models\StockCardCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user()->load(['assignment.section']);
})->middleware('auth:sanctum');


Route::group([
    'middleware' => ['auth:sanctum']
], function () {

    // Delivery Group
    Route::group([
        'prefix' => 'deliveries'
    ], function () {
        Route::get('list',[DeliveryController::class,'list']);
        Route::post('create',[DeliveryController::class,'create']);
        Route::post('update',[DeliveryController::class,'update']);
        Route::get('find',[DeliveryController::class,'find']);
        Route::get('iar',[DeliveryController::class,'getIARData']);
        Route::get('nod/find',[DeliveryController::class,'nodFindIAR']);
        Route::post('accept_reject',[DeliveryController::class,'acceptRejectDelivery']);
    });

    // Stock Card Group
    Route::group([
        'prefix' => 'stock_cards'
    ], function () {
        Route::get('list',[StockCardController::class,'list']);
        Route::post('create',[StockCardController::class,'create']);
        Route::get('find',[StockCardController::class,'find']);
        Route::post('update',[StockCardController::class,'update']);
        Route::post('issue',[StockCardController::class,'issue']);
        Route::post('stock/item',[StockCardController::class,'createItemStockCard']);
    });

    // Property Group
    Route::group([
        'prefix' => 'properties'
    ], function () {
        Route::get('list',[PropertyController::class,'list']);
        Route::post('create',[PropertyController::class,'create']);
        Route::get('find/property_no',[PropertyController::class,'findByPropertyNo']);
        Route::post('transfer',[PropertyController::class,'transfer']);
        Route::get('users',[PropertyController::class,'fetchUserProperties']);
    });


    // Fund Source Group
    Route::group([
        'prefix' => 'fund_sources'
    ], function () {
        Route::get('selection',[FundSourceController::class,'selection']);
    });

    // Sections Group
    Route::group([
        'prefix' => 'sections'
    ], function () {
        Route::get('selection',[SectionController::class,'selection']);
        Route::get('selection/personnel',[SectionController::class,'personnelSelection']);
    });

    // Sections Group
    Route::group([
        'prefix' => 'measurement_units'
    ], function () {
        Route::get('selection',[MeasurementUnitController::class,'selection']);
    });

     // Warehouse Group
    Route::group([
        'prefix' => 'warehouses'
    ], function () {
        Route::get('selection',[WarehouseController::class,'selection']);
    });

     // Stock Card Categories Group
    Route::group([
        'prefix' => 'stock_card_categories'
    ], function () {
        Route::get('selection',[StockCardCategoryController::class,'selection']);
    });

     // User Group
    Route::group([
        'prefix' => 'users'
    ], function () {
        Route::get('selection',[UserController::class,'selection']);
    });

    // Category Group
    Route::group([
        'prefix' => 'categories'
    ], function () {
        Route::get('selection',[CategoryController::class,'selection']);
    });

    // Notification Group
    Route::group([
        'prefix' => 'notifications'
    ], function () {
        Route::get('list',[NotificationController::class,'list']);
    });

    Route::get('dashboard',[DashboardController::class,'getDashboardData']);
    
    Route::post('logout',[AuthenticationController::class,'logout']);

});

Route::post('/login',[AuthenticationController::class,'login']);


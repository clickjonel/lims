<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StockCard extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_stock_cards';
    public $timestamps = false;

    protected $fillable = [
        'stock_no',
        'stock_name',
        'contract_no',
        'entity_name',
        'iar_no',
        'supplier_name',
        'supplier_address',
        'item_description',
        'dosage_form',
        'dosage_strength',
        'measurement_unit',
        'unit_cost',
        'procurement_mode',
        'fund_cluster',
        'warehouse',
        'batch_no',
        'expiry_date',
        'category',
        'req_office',
        'quantity',
    ];

    protected $appends = [
        // 'remaining'
    ];

    public function stockCardTransactions(): HasMany
    {
        return $this->hasMany(StockCardTransaction::class, 'stock_card_id');
    }

    public function latestTransaction(): HasOne // Change return type to HasOne
    {
        return $this->hasOne(StockCardTransaction::class, 'stock_card_id')->latestOfMany('id');
      
    }

    public function measurementUnit():BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit', 'id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'req_office');
    }
    
    
}

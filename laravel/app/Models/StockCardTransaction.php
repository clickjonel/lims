<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCardTransaction extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_stock_card_transactions';
    public $timestamps = false;

    protected $fillable = [
        'stock_card_id',
        'transaction_date',
        'received',
        'issued',
        'balance',
        'total_cost',
        'ptr_no',
        'iar_no',
        'recepient',
        'remarks'
    ];
    

    public function stockCard()
    {
        return $this->belongsTo(StockCard::class,'stock_card_id');
    }

}

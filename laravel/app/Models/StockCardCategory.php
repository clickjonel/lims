<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCardCategory extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_stock_card_categories';
    public $timestamps = false;
    
}

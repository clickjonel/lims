<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundSource extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_fund_sources';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'code'
    ];
}

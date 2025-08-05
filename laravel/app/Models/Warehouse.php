<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_warehouses';
    public $timestamps = false;
}

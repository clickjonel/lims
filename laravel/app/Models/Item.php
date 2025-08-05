<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'dohis';
    protected $table = 'dohis_item';
    protected $primaryKey = 'item_id';
    public $timestamps = false;
}

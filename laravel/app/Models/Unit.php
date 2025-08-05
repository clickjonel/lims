<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $connection = 'dohis';
    protected $table = 'dohis_unit';
    protected $primaryKey = 'unit_id';
    public $timestamps = false;
}

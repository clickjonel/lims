<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_measurements';
    public $timestamps = false;
    
    protected $fillable = [
        'name'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'lims';
    protected $table = 'categories';
    protected $fillable = [
        'name'
    ];
    
}

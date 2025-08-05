<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyUserHistory extends Model
{
    protected $connection = 'lims';
    protected $table = 'property_user_history';
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'user_id',
        'acquisition_date',
        'return_date',
        'remarks'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

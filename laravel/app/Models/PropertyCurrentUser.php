<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyCurrentUser extends Model
{
    protected $connection = 'lims';
    protected $table = 'property_user';
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'user_id',
        'issuance_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}

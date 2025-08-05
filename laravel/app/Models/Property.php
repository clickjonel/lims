<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_properties';
    public $timestamps = false;

    protected $fillable = [
        'property_no',
        'measurement_unit',
        'particulars',
        'unit_cost',
        'status',
        'remarks',
        'main_category_id'
    ];

    public function currentUser()
    {
        return $this->hasOne(PropertyCurrentUser::class, 'property_id');
    }

    public function history()
    {
        return $this->hasMany(PropertyUserHistory::class, 'property_id');
    }

    public function firstUser()
    {
        return $this->hasOne(PropertyUserHistory::class, 'property_id')->oldestOfMany();
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class,'measurement_unit');
    }

}


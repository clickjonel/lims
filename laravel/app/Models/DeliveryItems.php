<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryItems extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_delivery_items';
    public $timestamps = false;

    protected $fillable = [
        'delivery_id',
        'availability',
        'manufacturer',
        'manufacturing_date',
        'expiry_date',
        'unit_cost',
        'quantity',
        'batch_lot_number',
        'shelf_life',
        'measurement_unit',
        'description',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }

    public function measurementUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class,'measurement_unit');
    }


    // scopes
    public function scopeDelivered($query)
    {
        return $query->with(['measurementUnit'])->where('availability', 1);
    }

    public function scopeBalance($query)
    {
        return $query->with(['measurementUnit'])->where('availability', 2);
    }
}

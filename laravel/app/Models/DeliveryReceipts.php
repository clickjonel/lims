<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryReceipts extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_delivery_receipts';
    public $timestamps = false;

    protected $fillable = [
        'delivery_id',
        'dr_no',
        'dr_date',
        'delivery_date',
        'delivery_place'
    ];

    protected $hidden = [
        'delivery_id'
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class,'delivery_id');
    }
}

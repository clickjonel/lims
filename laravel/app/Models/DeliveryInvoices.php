<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryInvoices extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_delivery_invoices';
    public $timestamps = false;

    protected $fillable = [
        'delivery_id',
        'invoice_no',
        'invoice_date'
    ];

    protected $hidden = [
        'delivery_id'
    ];
}

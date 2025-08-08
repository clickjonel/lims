<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Delivery extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_deliveries';
    protected $fillable = [
        'hashid',
        'entity_name',
        'fund_source',
        'source_name',
        'source_address',
        'iar_no',
        'iar_date',
        'po_no',
        'po_date',
        'ptr_no',
        'ptr_date',
        'bl_no',
        'bl_date',
        'dnf_no',
        'dnf_date',
        'req_office',
        'end_user',
        'payment_term',
        'completion',
        'purpose',
        'iar_accepted',
        'rejection_reason'
    ];

    protected $appends = [
        'payment_term_label',
        'completion_label'
    ];

    public const PAYMENT_TERM_CHARGED = 1;
    public const PAYMENT_TERM_DONATED = 2;

    public const PAYMENT_TERM_LABELS = [
        self::PAYMENT_TERM_CHARGED => 'Charge',
        self::PAYMENT_TERM_DONATED   => 'Donation',
    ];


    public const COMPLETION_PARTIAL = 1;
    public const COMPLETION_COMPLETE = 2;
    public const COMPLETION_COMPLETION = 3;

    public const COMPLETION_LABELS = [
        self::COMPLETION_PARTIAL => 'Partial',
        self::COMPLETION_COMPLETE   => 'Completed',
        self::COMPLETION_COMPLETION   => 'Completion',
    ];
 

    // attribute accessors
    public function getPaymentTermLabelAttribute(): string
    {
        return self::PAYMENT_TERM_LABELS[$this->payment_term];
    }

    public function getCompletionLabelAttribute(): string
    {
        return self::COMPLETION_LABELS[$this->completion];
    }


    // relationships
    public function fundSource(): BelongsTo
    {
        return $this->belongsTo(FundSource::class,'fund_source');
    }

    public function requisitioningSection(): BelongsTo
    {
        return $this->belongsTo(Section::class,'req_office');
    }

    public function endUser(): BelongsTo
    {
        return $this->belongsTo(User::class,'end_user');
    }

    public function deliveryReceipts(): HasMany
    {
        return $this->hasMany(DeliveryReceipts::class,'delivery_id');
    }

    public function deliveryInvoices(): HasMany
    {
        return $this->hasMany(DeliveryInvoices::class,'delivery_id');
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(DeliveryItems::class,'delivery_id');
    }

    public function delivered_items()
    {
        return $this->hasMany(DeliveryItems::class)->delivered();
    }

    public function balance_items()
    {
        return $this->hasMany(DeliveryItems::class)->balance();
    }
    
}

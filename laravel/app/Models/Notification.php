<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'lims';
    protected $table = 'lims_notifications';
    
    protected $fillable = [
        'notifiable_id',
        'notifiable_class',
        'path',
        'marked_read',
        'user_id',
        'section_id',
    ];
}

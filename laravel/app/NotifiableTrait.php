<?php

namespace App;

use App\Models\Notification;

trait NotifiableTrait
{
    public function createNotification($notifData)
    {
        Notification::create($notifData);
    }
}

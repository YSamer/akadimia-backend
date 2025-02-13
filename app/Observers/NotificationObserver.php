<?php

namespace App\Observers;

use App\Models\Notification;
use App\Traits\PushNotification;

class NotificationObserver
{
    use PushNotification;
    /**
     * Handle the Notification "created" event.
     */
    public function created(Notification $notification): void
    {
        // Notification::create($notification->toArray());
        $this->sendNewNotification($notification);
    }

    /**
     * Handle the Notification "updated" event.
     */
    public function updated(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "deleted" event.
     */
    public function deleted(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "restored" event.
     */
    public function restored(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     */
    public function forceDeleted(Notification $notification): void
    {
        //
    }
}

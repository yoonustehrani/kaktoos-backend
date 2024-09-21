<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class SMSChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // $message = $notification->toVoice($notifiable);
 
        // Send notification to the $notifiable instance...
    }
}
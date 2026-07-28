<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $icon;

    public function __construct($title, $message, $icon = 'ph-bell')
    {
        $this->title = $title;
        $this->message = $message;
        $this->icon = $icon;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
        ];
    }
}

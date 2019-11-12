<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderStatusBrodcast extends Notification
{
    use Queueable;


    public $orderStatus;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($orderStatus)
    {
        $this->orderStatus=$orderStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'orderId' => "123455",
            'orderStatus' => $this->orderStatus,
        ]);
    }

    public function toArray($notifiable)
    {
        Log::info("to broadcast");
        return new BroadcastMessage([
            'orderId' => "123455",
            'orderStatus' => $this->orderStatus,
        ]);
    }

    public function broadcastWith()
    {
        Log::info("to broadcastWith");
        return [
            'data' => $this->orderStatus
        ];
    }
}

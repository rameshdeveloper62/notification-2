<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $orderStatus;
    public $userId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($orderStatus,$userId)
    {
        $this->orderStatus=$orderStatus;
        $this->userId=$userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        Log::info("order status");
        // return new Channel('order-status');
        return new Channel('everywhere');
        // return new PrivateChannel('App.User.'.$this->userId);
    }

    public function broadcastWith()
    {
        return [
            'data' =>auth()->user()
        ];
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomBillUpdated implements ShouldBroadcastNow
{
    public int $deviceId;
    public int $hour;
    public float $value;
    public float $bill;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct($deviceId, $hour, $value, $bill)
    {
        $this->deviceId = $deviceId;
        $this->hour = $hour;
        $this->bill = $bill;
        $this->value = $value;
    }
    public function broadcastWith(): array
    {
        return [
            $this->hour,
            $this->value,
            $this->bill,
        ];
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('data-updated.'.$this->deviceId);
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorDataUpdated implements ShouldBroadcastNow
{
    public int $deviceId;
    public string $category;
    public float $value;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct($deviceId, $category, $value)
    {
        $this->deviceId = $deviceId;
        $this->category = $category;
        $this->value = $value;
    }

    public function broadcastWith(): array
    {
        return [
            $this->category,
            $this->value
        ];
    }

    /**
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('data-updated.'.$this->deviceId);
    }
}

<?php

namespace App\Events;

use App\Models\Relay;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RelayStatusUpdated implements ShouldBroadcast
{
    public int $deviceId;
    public int $relayNumber;
    public bool $relayStatus;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct($deviceId, $relayNumber, $relayStatus)
    {
        $this->deviceId = $deviceId;
        $this->relayNumber = $relayNumber;
        $this->relayStatus = $relayStatus;
    }

    public function broadcastWith(): array
    {
        return [
            $this->relayNumber,
            $this->relayStatus
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('data-updated.'.$this->deviceId);
    }
}

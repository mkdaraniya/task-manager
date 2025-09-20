<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stats;
    public $recentActivities;

    public function __construct($stats, $recentActivities)
    {
        $this->stats = $stats;
        $this->recentActivities = $recentActivities;
    }

    public function broadcastOn()
    {
        return new Channel('dashboard.' . auth()->id());
    }

    public function broadcastWith()
    {
        return [
            'stats' => $this->stats,
            'recentActivities' => $this->recentActivities,
        ];
    }
}

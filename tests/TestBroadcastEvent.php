<?php

namespace Igniter\Broadcast\Tests;

use Igniter\Flame\Database\Model;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TestBroadcastEvent implements ShouldBroadcast
{
    use Queueable, SerializesModels;

    public function __construct(
        public ?Model $user = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.user.'.$this->user?->user_id),
        ];
    }
}

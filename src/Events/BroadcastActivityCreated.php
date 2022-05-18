<?php

namespace Igniter\Broadcast\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class BroadcastActivityCreated implements ShouldBroadcast
{
    use Queueable, SerializesModels;

    /**
     * The notification instance.
     *
     * @var \Igniter\Flame\ActivityLog\Models\Activity
     */
    public $activity;

    /**
     * BroadcastActivitySent constructor.
     * @param $activity
     */
    public function __construct($activity)
    {
        $this->activity = $activity;
    }

    public function broadcastWhen()
    {
        return $this->activity->user_type === 'users';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|Channel[]
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('admin.user.'.$this->activity->user_id)];
    }

    public function broadcastAs()
    {
        return 'activityCreated';
    }

    /**
     * Get the data that should be sent with the broadcasted event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return array_merge($this->activity->toArray(), [
            'id' => $this->activity->activity_id,
            'type' => $this->activity->type,
            'title' => $this->activity->title,
            'url' => $this->activity->url,
            'message' => strip_tags($this->activity->message),
        ]);
    }
}

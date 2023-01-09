---
title: "Broadcast Extension"
section: "extensions"
sortOrder: 30
---

## Introduction

This extension integrates with [Laravel Broadcasting](https://laravel.com/docs/broadcasting) to allow you to receive
browser notifications when certain events happen in TastyIgniter. Now you can receive instant updates directly from your
website to all of your devices.

## Installation

To install this extension, click on the **Add to Site** button on the TastyIgniter marketplace item page or search
for **Igniter.Broadcast** in **Admin System > Updates > Browse Extensions**

## Configuration

1. You need to fill in your applicable Pusher credentials under
`System > Settings > Broadcast Events settings`. Follow the instructions to fill in your applicable Pusher credentials, you will need your Pusher Secret, Pusher App ID, and Pusher key.
2. Install the Igniter.User extension as they are required for authenticating private and presence channels. This step is optional if you only want to use public channels.

## Usage

**Example of Registering Event Broadcast**

Here is an example of an extension registering an event broadcast class to be dispatched when system
event `activityLogger.logCreated` is fired.

```
public function registerEventBroadcasts()
{
    return [
        'activityLogger.logCreated' => \Igniter\Broadcast\Events\BroadcastActivityCreated::class,
    ];
}
```

**Example of an Event Class**

An event broadcast class should implement `Illuminate\Contracts\Broadcasting\ShouldBroadcast`.

```
class BroadcastActivityCreated implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast
{
    use Queueable, SerializesModels;

    /**
     * The activity model instance.
     *
     * @var \Igniter\Flame\ActivityLog\Models\Activity
     */
    public $activity;

    /**
     * BroadcastActivityCreated constructor.
     * @param $activity
     */
    public function __construct($activity)
    {
        $this->activity = $activity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|Channel[]
     */
    public function broadcastOn()
    {
        return new PrivateChannel('admin.user.'.$this->activity->user_id);
    }
}
```

For more information
see [Defining Broadcast Events](https://laravel.com/docs/5.5/broadcasting#defining-broadcast-events)

**Listening For Events on a User Authenticated Channel**

```
Broadcast.user()
    .listen('eventName', (e) => {
        console.log(e);
    })
```

**Listening For Events on a Public Channel**

```
Broadcast.channel('channelName')
    .listen('eventName', (e) => {
        console.log(e);
    })
```

**Use this PHP to manually dispatch a broadcast event:**

```
Event::dispatch(new BroadcastActivityCreated($activity));
```

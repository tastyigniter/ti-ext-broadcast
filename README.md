### Introduction

The TastyIgniter Broadcast extension leverages [Laravel Broadcasting](https://laravel.com/docs/broadcasting) to deliver real-time browser notifications in response to specific events within TastyIgniter. This extension allows you to broadcast system events such as order status updates, reservation notifications, and more to your TastyIgniter website.

While the extension is designed to integrate with the [Pusher service](https://pusher.com/), it also supports other Laravel broadcast drivers.

## Installation

You can install the extension via composer using the following command:

```bash
composer require tastyigniter/ti-ext-broadcast:"^4.0" -W
```

By default, the extension registers the `/broadcasting/auth` route to handle authorization requests

## Getting started

1. Navigate to _Admin > Manage > Settings > Broadcast Events_ to configure the extension. You will need to enter your Pusher App ID, Pusher Key, and Pusher Secret.
2. You must configure and run a queue worker to process broadcast jobs. You can read more about this in the [Queue worker section of the TastyIgniter installation documentation](https://tastyigniter.com/docs/installation#setting-up-the-queue-deamon).

## Usage

The Broadcast extension handles authorisation for the following broadcast channels:

- `main.user.{userId}`: A private channel for broadcasting events to a specific customer.
- `admin.user.{userId}`: A private channel for broadcasting events to a specific staff member.

You can broadcast your custom events directly from your code, or you can broadcast other system events by [registering event broadcasts class](#registering-event-broadcasts).

### Defining broadcast events

To broadcast an event, you need to define an event broadcast class that implements the `Illuminate\Contracts\Broadcasting\ShouldBroadcast` interface. The event broadcast class should define a `broadcastOn` method that returns the channels the event should be broadcast on.

An event broadcast class is typically stored in the `src/Events` directory of your extension.

```php
namespace Author\Extension\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Queueable, SerializesModels;

    public function __construct(
        public Activity $activity
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.user.'.$this->activity->user_id)
        ];
    }
}
```

In this example, the event is broadcast on a private channel named `admin.user.{user_id}`.

For more details, refer to [Laravel's Defining Broadcast Events documentation](https://laravel.com/docs/broadcasting#defining-broadcast-events).

### Registering event broadcasts

Once you have defined an event broadcast class, you need to register the event broadcast class to be broadcast when a system event is fired. You can do this by defining a `registerEventBroadcasts` method on your extension class. The method should return an array of system event aliases and their corresponding event broadcast classes:

```php
public function registerEventBroadcasts()
{
    return [
        'igniter.cart.orderStatusAdded' => \Author\Extension\Events\OrderStatusUpdated::class,
    ];
}
```

In this example, the `OrderStatusUpdated` event will be broadcast when the `igniter.cart.orderStatusAdded` system event is fired.

### Broadcasting events

By default, TastyIgniter will automatically broadcast registered event classes when the associated system event is fired. However, you can also manually broadcast events using the event's `dispatch` method.

```php
use Author\Extensions\Events\OrderStatusUpdated;

OrderStatusUpdated::dispatch($activity);
```

### Receiving broadcasts

You can listen for events on user authenticated channels or public channels as follows:

```javascript
// User Authenticated Channel
Broadcast.user()
    .listen('eventName', (e) => {
        console.log(e);
    })

// Public Channel
Broadcast.channel('channelName')
    .listen('eventName', (e) => {
        console.log(e);
    })
```

## Changelog

Please see [CHANGELOG](https://github.com/tastyigniter/ti-ext-broadcast/blob/master/CHANGELOG.md) for more information on what has changed recently.

## Reporting issues

If you encounter a bug in this extension, please report it using the [Issue Tracker](https://github.com/tastyigniter/ti-ext-broadcast/issues) on GitHub.

## Contributing

Contributions are welcome! Please read [TastyIgniter's contributing guide](https://tastyigniter.com/docs/contribution-guide).

## Security vulnerabilities

For reporting security vulnerabilities, please see our [our security policy](https://github.com/tastyigniter/ti-ext-broadcast/security/policy).

### License

TastyIgniter Broadcast extension is open-source software licensed under the [MIT license](https://github.com/tastyigniter/ti-ext-broadcast/blob/master/LICENSE.md).

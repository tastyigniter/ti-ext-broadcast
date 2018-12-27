# TastyIgniter Pusher Integration

### Installation

1. From the extension's admin settings page, fill in your applicable Pusher credentials. You will need your Pusher Secret, Pusher App ID, and Pusher key.
2. Install the Igniter.User extension as they are dependencies for authenticating private and presence channels. This step is optional if you only want to use public channels.
2. Add the Pusher component included with this extension to a layout or page. The component includes the pusher js library.

### Usage

**Use this JS to create a Pusher object:**

```
var pusher = new Pusher('YOUR_PUSHER_KEY_GOES_HERE', {
    encrypted: true
});
```

**Use this JS to connect to a Pusher public channel and bind to an event:**

```
var channel = pusher.subscribe('PUBLIC_CHANNEL_NAME');
channel.bind('test', function(data) {
    console.log("Test: " + data);
});
```

**Use this JS to connect and authenticate a Pusher private channel and bind to an event:**

```
var privateChannel = pusher.subscribe("private-PRIVATE_CHANNEL_NAME");
privateChannel.bind('test', function(data) {
    console.log("PRIVATE - test: " + data);
});
```

**Use this JS to connect and authenticate a Pusher presence channel and bind to an event:**

```
var presenceChannel = pusher.subscribe('presence-PRESENCE_CHANNEL_NAME');
presenceChannel.bind('test', function(data) {
    console.log("PRESENCE - test: " + data);
});
```

**Use this PHP to trigger an event to a pusher channel:**

```
Pusher::instance()->trigger($channel_name, $event_name, $data);
```
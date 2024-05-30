+function ($) {
    "use strict"

    window.Broadcast = {
        Echo: undefined,

        event: undefined,

        init: function () {
            if (Broadcast.Echo === undefined)
                return;

            if (!app.broadcast.pusherUserChannel)
                return;

            $('[data-control="notification-list"]').on('click', '[data-bs-toggle="dropdown"]', function () {
                Broadcast.checkNotificationPermission()
            });

            Broadcast.user().notification(Broadcast.pushNotification)
        },

        channel: function (name) {
            return Broadcast.Echo.channel(name)
        },

        user: function () {
            return Broadcast.Echo.private(app.broadcast.pusherUserChannel)
        },

        pushNotification: function (notification) {
            var permissionLevel = Push.Permission.get()

            // Let's check if the browser supports notifications
            if (!("Notification" in window)) {
                throw new Error("This browser does not support desktop notification");
            }
            // Let's check whether notification permissions have already been granted
            else if (permissionLevel === Push.Permission.GRANTED) {
                Broadcast.createNotification(notification)
            }
        },

        checkNotificationPermission: function () {
            var permissionLevel = Push.Permission.get()

            if (permissionLevel !== Push.Permission.DENIED && permissionLevel !== Push.Permission.GRANTED) {
                Push.Permission.request(Broadcast.permissionGranted, Broadcast.permissionDenied)
            }
        },

        permissionGranted: function (permission) {
        },

        permissionDenied: function (permission) {
        },

        createNotification: function (notification) {
            Push.create(notification.title, {
                body: notification.message,
                timeout: 16000,
                onClick: function () {
                    window.focus();
                    window.location = notification.url
                    this.close();
                }
            });
        }
    }

    if (window.app !== undefined && window.app.broadcast !== undefined) {
        Broadcast.Echo = new Echo({
            broadcaster: 'pusher',
            key: app.broadcast.pusherKey,
            cluster: app.broadcast.pusherCluster,
            authEndpoint: app.broadcast.pusherAuthUrl,
            encrypted: app.broadcast.pusherEncrypted,
        });
    }

    $(document).ready(function () {
        Broadcast.init()
    })
}(jQuery)

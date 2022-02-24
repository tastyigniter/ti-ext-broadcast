+function ($) {
    "use strict"

    window.Broadcast = {
        Echo: undefined,

        event: undefined,

        init: function () {
            if (Broadcast.Echo === undefined)
                return;

            Broadcast.user().listen('.activityCreated', Broadcast.onActivityCreated)

            $(window).on('broadcastNewActivity', Broadcast.reloadActivityMenu)
                .on('broadcastNewActivity', Broadcast.pushNotification)
        },

        channel: function(name) {
            return Broadcast.Echo.channel(name)
        },

        user: function() {
            return Broadcast.Echo.private(app.broadcast.pusherUserChannel)
        },

        onActivityCreated: function (event) {
            Broadcast.event = event

            var _event = jQuery.Event('broadcastNewActivity')
            $(window).trigger(_event, [event])
        },

        reloadActivityMenu: function () {
            var $mainMenu = $('[data-control="mainmenu"]')

            if ($mainMenu.length) {
                $mainMenu.mainMenu('clearOptions', 'activity')
                $mainMenu.mainMenu('updateBadgeCount', 'activity', 1)
            }
        },

        pushNotification: function (e, event) {
            var permissionLevel = Push.Permission.get()

            // Let's check if the browser supports notifications
            if (!("Notification" in window)) {
                throw new Error("This browser does not support desktop notification");
            }
            // Let's check whether notification permissions have already been granted
            else if (permissionLevel === Push.Permission.GRANTED) {
                // If it's okay let's create a notification
                Broadcast.createNotification()
            }
            // Otherwise, we need to ask the user for permission
            else if (permissionLevel !== Push.Permission.DENIED) {
                Push.Permission.request(Broadcast.permissionGranted, Broadcast.permissionDenied)
            }
        },

        permissionGranted: function (permission) {
            Broadcast.createNotification()
        },

        permissionDenied: function (permission) {

        },

        createNotification: function () {
            Push.create(Broadcast.event.title, {
                body: Broadcast.event.message,
                timeout: 16000,
                onClick: function () {
                    window.focus();
                    window.location = Broadcast.event.url
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

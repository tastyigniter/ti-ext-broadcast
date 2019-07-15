+function ($) {
    "use strict"

    $(document).render(function () {
        const pusherPromise = $.Deferred();

        $.getScript('//js.pusher.com/4.3/pusher.min.js', () => {
            const socket = new Pusher(app.pusherKey, {
                authEndpoint: app.pusherAuthUrl,
                cluster: app.pusherCluster,
                auth: {
                    headers: {
                        'X-CSRF-Token': app.pusherCsrfToken
                    }
                }
            });

            pusherPromise.resolve({
                main: socket.subscribe('public'),
                user: app.pusherUser ? socket.subscribe('private-user' + app.pusherUser.user_id) : null
            });
        });

        var pusher = pusherPromise;

        pusher.then(function (channels) {
            if (channels.main) {
                channels.main.bind('newOrder', function (data) {
                    console.log("Test: " + data);
                })
            }
            if (channels.user) {
                channels.user.bind('notification', function (data) {
                    console.log("PRIVATE - test: " + data);
                })
            }
        })
    })

}(jQuery)

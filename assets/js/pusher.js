+function ($) {
    "use strict"

    $(document).render(function () {
        const pusherPromise = $.Deferred();

        $.getScript('//js.pusher.com/4.3/pusher.min.js', () => {
            // const socket = new Pusher(app.forum.attribute('pusherKey'), {
            //     authEndpoint: app.forum.attribute('apiUrl') + '/pusher/auth',
            //     cluster: app.forum.attribute('pusherCluster'),
            //     auth: {
            //         headers: {
            //             'X-CSRF-Token': app.session.csrfToken
            //         }
            //     }
            // });

            // pusherPromise.resolve({
            //     main: socket.subscribe('public'),
            //     user: app.session.user ? socket.subscribe('private-user' + app.session.user.id()) : null
            // });
        });

        var pusher = pusherPromise;
        console.log(pusher)
    })

}(jQuery)

<?php

Route::group([
    'as' => 'igniter.pusher.auth',
    'middleware' => ['web'],
], function () {
    Route::post('pusher/auth', function () {
        return \Igniter\Pusher\Classes\Pusher::runEntryPoint();
    });

    Route::post(config('system.adminUri', '/admin').'/pusher/auth', function () {
        return \Igniter\Pusher\Classes\Pusher::runEntryPoint();
    });
});
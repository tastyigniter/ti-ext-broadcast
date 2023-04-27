<?php

use Igniter\Flame\Igniter;

Route::group([
    'middleware' => ['web'],
], function ($router) {
    $router
        ->name('igniter.broadcast.auth')
        ->post('broadcasting/auth', '\\'.\Igniter\Broadcast\Classes\Controller::class.'@auth');
    $router
        ->name('igniter.broadcast.admin.auth')
        ->post(Igniter::uri().'/broadcasting/auth', '\\'.\Igniter\Broadcast\Classes\Controller::class.'@auth');
});

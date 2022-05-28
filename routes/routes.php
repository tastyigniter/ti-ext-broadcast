<?php

use Igniter\Flame\Igniter;

Route::group([
    'as' => 'igniter.broadcast.auth',
    'middleware' => ['web'],
], function ($router) {
    $router->post('broadcasting/auth', '\\'.\Igniter\Broadcast\Classes\Controller::class.'@auth');
    $router->post(Igniter::uri().'/broadcasting/auth', '\\'.\Igniter\Broadcast\Classes\Controller::class.'@auth');
});

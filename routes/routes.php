<?php

Route::group([
    'as' => 'igniter.broadcast.auth',
    'middleware' => ['web'],
], function ($router) {
    $router->post('broadcasting/auth', '\\'.\Igniter\Broadcast\Classes\Controller::class.'@auth');
    $router->post(config('igniter.routes.adminUri', '/admin').'/broadcasting/auth', '\\'.\Igniter\Broadcast\Classes\Controller::class.'@auth');
});

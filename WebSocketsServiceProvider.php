<?php

namespace Igniter\Broadcast;

class WebSocketsServiceProvider extends \BeyondCode\LaravelWebSockets\WebSocketsServiceProvider
{
    protected function registerDashboardGate()
    {
//        Gate::define('viewWebSocketsDashboard', function ($user = null) {
//            return app()->environment('local');
//        });

        return $this;
    }
}

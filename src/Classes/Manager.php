<?php

namespace Igniter\Broadcast\Classes;

use App\Providers\BroadcastServiceProvider;
use Igniter\Admin\Classes\AdminController;
use Igniter\Broadcast\Models\Settings;
use Igniter\Flame\Igniter;
use Igniter\Main\Classes\MainController;
use Igniter\System\Facades\Assets;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Facades\Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;

class Manager
{
    public static function bindBroadcasts(array $broadcasts)
    {
        foreach ($broadcasts as $eventCode => $broadcastClass) {
            self::bindBroadcast($eventCode, $broadcastClass);
        }
    }

    public static function bindBroadcast($eventCode, $broadcastClass)
    {
        Event::listen($eventCode, function() use ($broadcastClass) {
            self::dispatch($broadcastClass, func_get_args());
        });
    }

    public static function register(Application $app)
    {
        $app->resolving(\Illuminate\Broadcasting\BroadcastManager::class, function() use ($app) {
            $app->config->set('broadcasting.default', Settings::get('driver', 'pusher'));
            $app->config->set('broadcasting.connections.pusher.key', Settings::get('key'));
            $app->config->set('broadcasting.connections.pusher.secret', Settings::get('secret'));
            $app->config->set('broadcasting.connections.pusher.app_id', Settings::get('app_id'));
            $app->config->set('broadcasting.connections.pusher.options.cluster', Settings::get('cluster'));
            $app->config->set('broadcasting.connections.pusher.options.encrypted', (bool)Settings::get('encrypted'));
        });
    }

    public static function boot(Application $app)
    {
        if (!Igniter::hasDatabase() || !Settings::isConfigured()) {
            return;
        }

        self::bindBroadcasts(Settings::findEventBroadcasts());

        if (!$app->providerIsLoaded(BroadcastServiceProvider::class)) {
            Broadcast::routes();
        }

        Broadcast::channel('admin.users.{userId}', function($user, $userId) {
            return (int)$user->user_id === (int)$userId;
        }, ['guards' => ['web', 'igniter-admin']]);

        Broadcast::channel('main.users.{userId}', function($user, $userId) {
            return (int)$user->customer_id === (int)$userId;
        }, ['guards' => ['web', 'igniter-customer']]);

        AdminController::extend(function(AdminController $controller) {
            $controller->bindEvent('controller.beforeRemap', function() use ($controller) {
                self::addAssetsToController($controller);
            });
        });

        MainController::extend(function(MainController $controller) {
            $controller->bindEvent('controller.beforeRemap', function() use ($controller) {
                self::addAssetsToController($controller);
            });
        });
    }

    public static function dispatch($broadcastClass, $params)
    {
        if (!class_exists($broadcastClass)) {
            throw new InvalidArgumentException("Event broadcast class '$broadcastClass' not found.");
        }

        return Event::dispatch(new $broadcastClass(...$params));
    }

    /**
     * @param $controller \Igniter\Admin\Classes\AdminController|\Igniter\Main\Classes\MainController
     */
    protected static function addAssetsToController($controller)
    {
        $channelName = null;
        if ($controller instanceof AdminController && AdminAuth::isLogged()) {
            $channelName = AdminAuth::user()->receivesBroadcastNotificationsOn();
        } elseif ($controller instanceof MainController && Auth::isLogged()) {
            $channelName = Auth::user()->receivesBroadcastNotificationsOn();
        }

        Assets::putJsVars(['broadcast' => [
            'pusherKey' => config('broadcasting.connections.pusher.key'),
            'pusherCluster' => config('broadcasting.connections.pusher.options.cluster'),
            'pusherEncrypted' => config('broadcasting.connections.pusher.options.encrypted'),
            'pusherAuthUrl' => url('broadcasting/auth'),
            'pusherUserChannel' => $channelName,
        ]]);

        $controller->addJs('igniter.broadcast::/js/vendor/pusher/pusher.min.js', 'pusher-js');
        $controller->addJs('igniter.broadcast::/js/vendor/echo/echo.iife.js', 'echo-js');
        $controller->addJs('igniter.broadcast::/js/vendor/push/push.min.js', 'push-js');
        $controller->addJs('igniter.broadcast::/js/broadcast.js', 'broadcast-js');
    }
}

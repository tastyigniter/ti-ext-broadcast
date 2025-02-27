<?php

declare(strict_types=1);

namespace Igniter\Broadcast\Classes;

use Igniter\Admin\Classes\AdminController;
use Igniter\Broadcast\Models\Settings;
use Igniter\Flame\Support\Facades\Igniter;
use Igniter\Main\Classes\MainController;
use Igniter\System\Facades\Assets;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Facades\Auth;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;

class Manager
{
    public static function bindBroadcasts(array $broadcasts): void
    {
        foreach ($broadcasts as $eventCode => $broadcastClass) {
            self::bindBroadcast($eventCode, $broadcastClass);
        }
    }

    public static function bindBroadcast($eventCode, $broadcastClass): void
    {
        Event::listen($eventCode, function() use ($broadcastClass): void {
            self::dispatch($broadcastClass);
        });
    }

    public static function register(Application $app): void
    {
        $app->resolving(BroadcastManager::class, function() use ($app): void {
            if (Igniter::hasDatabase() && Settings::isConfigured()) {
                $app->config->set('broadcasting.default', Settings::get('driver', 'pusher'));
                $app->config->set('broadcasting.connections.pusher.key', Settings::get('key'));
                $app->config->set('broadcasting.connections.pusher.secret', Settings::get('secret'));
                $app->config->set('broadcasting.connections.pusher.app_id', Settings::get('app_id'));
                $app->config->set('broadcasting.connections.pusher.options.cluster', $cluster = Settings::get('cluster'));
                $app->config->set('broadcasting.connections.pusher.options.host', Settings::get('host') ?: 'api-'.$cluster.'.pusher.com');
                $app->config->set('broadcasting.connections.pusher.options.encrypted', (bool)Settings::get('encrypted'));
            }
        });
    }

    public static function boot(Application $app): void
    {
        if (!Igniter::hasDatabase() || !Settings::isConfigured()) {
            return;
        }

        self::bindBroadcasts(Settings::findEventBroadcasts());

        if (!$app->providerIsLoaded(BroadcastServiceProvider::class)) {
            Broadcast::routes();
        }

        Broadcast::channel(
            'admin.users.{userId}',
            fn($user, $userId): bool => (int)$user->user_id === (int)$userId, ['guards' => ['web', 'igniter-admin']],
        );

        Broadcast::channel(
            'main.users.{userId}',
            fn($user, $userId): bool => (int)$user->customer_id === (int)$userId, ['guards' => ['web', 'igniter-customer']],
        );

        AdminController::extend(function(AdminController $controller): void {
            $controller->bindEvent('controller.beforeRemap', function() use ($controller): void {
                self::addAssetsToController($controller);
            });
        });

        MainController::extend(function(MainController $controller): void {
            $controller->bindEvent('controller.beforeRemap', function() use ($controller): void {
                self::addAssetsToController($controller);
            });
        });
    }

    public static function dispatch($broadcastClass, $params = [])
    {
        if (!class_exists($broadcastClass)) {
            throw new InvalidArgumentException(sprintf("Event broadcast class '%s' not found.", $broadcastClass));
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

        $controller->addJs('igniter.broadcast::/js/vendor.js', 'broadcast-vendor-js');
        $controller->addJs('igniter.broadcast::/js/echo.js', 'broadcast-echo-js');
        $controller->addJs('igniter.broadcast::/js/broadcast.js', 'broadcast-js');
    }
}

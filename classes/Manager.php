<?php namespace Igniter\Broadcast\Classes;

use Admin\Classes\AdminController;
use AdminAuth;
use Assets;
use Auth;
use Broadcast;
use Event;
use Igniter\Broadcast\Models\Settings;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use System\Classes\BaseController;

class Manager
{
    use \Igniter\Flame\Traits\Singleton;

    /**
     * @param array $broadcasts
     */
    public static function bindBroadcasts(array $broadcasts)
    {
        foreach ($broadcasts as $eventCode => $broadcastClass) {
            self::bindBroadcast($eventCode, $broadcastClass);
        }
    }

    /**
     * @param $eventCode
     * @param $broadcastClass
     */
    public static function bindBroadcast($eventCode, $broadcastClass)
    {
        Event::listen($eventCode, function () use ($eventCode, $broadcastClass) {
            self::instance()->dispatch($broadcastClass, func_get_args());
        });
    }

    public function register(Application $app)
    {
        $app->resolving(\Illuminate\Broadcasting\BroadcastManager::class, function () use ($app) {
            $app->config->set('broadcasting.default', 'pusher');
            $app->config->set('broadcasting.connections.pusher.key', Settings::get('key'));
            $app->config->set('broadcasting.connections.pusher.secret', Settings::get('secret'));
            $app->config->set('broadcasting.connections.pusher.app_id', Settings::get('app_id'));
            $app->config->set('broadcasting.connections.pusher.options.cluster', Settings::get('cluster'));
            $app->config->set('broadcasting.connections.pusher.options.encrypted', (bool)Settings::get('encrypted'));
        });
    }

    public function boot(Application $app)
    {
        self::bindBroadcasts(Settings::findEventBroadcasts());

        Broadcast::channel('admin.user.{userId}', function ($user, $userId) {
            return (int)$user->user_id === (int)$userId;
        });

        Broadcast::channel('main.user.{userId}', function ($user, $userId) {
            return (int)$user->customer_id === (int)$userId;
        });

        BaseController::extend(function (BaseController $controller) {
            $controller->bindEvent('controller.afterConstructor', function ($controller) {
                $this->addAssetsToController($controller);
            });
        });
    }

    public function dispatch($broadcastClass, $params)
    {
        if (!class_exists($broadcastClass))
            throw new InvalidArgumentException("Event broadcast class '$broadcastClass' not found.");

        return Event::dispatch(new $broadcastClass(...$params));
    }

    /**
     * @param $controller \Admin\Classes\AdminController|\Main\Classes\MainController
     */
    protected function addAssetsToController($controller)
    {
        $channelName = 'main.user.'.(Auth::isLogged() ? Auth::getId() : 0);
        if ($controller instanceof AdminController)
            $channelName = 'admin.user.'.(AdminAuth::isLogged() ? AdminAuth::getId() : 0);

        Assets::putJsVars([
            'broadcast' => [
                'pusherKey' => Settings::get('key'),
                'pusherCluster' => Settings::get('cluster'),
                'pusherEncrypted' => Settings::get('encrypted'),
                'pusherAuthUrl' => $controller->pageUrl('broadcasting/auth'),
                'pusherUserChannel' => $channelName,
            ],
        ]);

        $controller->addMeta(['name' => 'csrf-token', 'content' => csrf_token()]);

        $controller->addJs('$/igniter/broadcast/assets/js/vendor/pusher/pusher.min.js', 'pusher-js');
        $controller->addJs('$/igniter/broadcast/assets/js/vendor/echo/echo.iife.js', 'echo-js');
        $controller->addJs('$/igniter/broadcast/assets/js/vendor/push/push.min.js', 'push-js');
        $controller->addJs('$/igniter/broadcast/assets/js/broadcast.js', 'broadcast-js');
    }
}
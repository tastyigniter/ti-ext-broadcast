<?php namespace Igniter\Pusher;

use Admin\Classes\AdminController;
use Igniter\Pusher\Classes\Pusher;
use Igniter\Pusher\Models\Settings;
use System\Classes\BaseExtension;

/**
 * Pusher Extension Information File
 */
class Extension extends BaseExtension
{
    public function boot()
    {
        $this->subscribeEvents();
    }

    /**
     * Registers any front-end components implemented in this extension.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Igniter\Pusher\Components\Pusher' => 'pusher',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'Pusher Settings',
                'description' => 'Manage pusher api and cluster settings.',
                'icon' => 'fa fa-angle-double-down',
                'model' => 'Igniter\Pusher\Models\Settings',
            ],
        ];
    }

    protected function subscribeEvents()
    {
        AdminController::extend(function ($controller) {
            $controller->bindEvent('controller.afterConstructor', function ($controller) {
                \Assets::putJsVars([
                    'pusherKey' => Settings::get('key'),
                    'pusherAuthUrl' => admin_url('pusher/auth'),
                    'pusherCluster' => Settings::get('cluster'),
                    'pusherCsrfToken' => csrf_token(),
                    'pusherUser' => \AdminAuth::user(),
                ]);

                \Assets::addJs('~/extensions/igniter/pusher/assets/js/admin-pusher.js', 'pusher.js');
            });
        });

        \Event::listen('notification.sending', function ($activity, $recipients) {
            $pusher = Pusher::instance();
            foreach ($recipients as $user) {
                $pusher->trigger('private-user'.$user->getKey(), 'notification', null);
            }
        });
    }
}

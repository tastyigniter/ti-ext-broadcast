<?php

namespace Igniter\Broadcast;

use Igniter\Broadcast\Classes\Manager;
use System\Classes\BaseExtension;

/**
 * Broadcast Extension Information File
 */
class Extension extends BaseExtension
{
    public function register()
    {
        Manager::instance()->register($this->app);
    }

    public function boot()
    {
        Manager::instance()->boot($this->app);
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'Broadcast Events Settings',
                'description' => 'Manage pusher api and cluster settings.',
                'icon' => 'fa fa-bullhorn',
                'model' => \Igniter\Broadcast\Models\Settings::class,
            ],
        ];
    }

    public function registerEventBroadcasts()
    {
        return [
            'activityLogger.logCreated' => \Igniter\Broadcast\Events\BroadcastActivityCreated::class,
        ];
    }
}

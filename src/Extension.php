<?php

namespace Igniter\Broadcast;

use Igniter\Broadcast\Classes\Manager;
use Igniter\System\Classes\BaseExtension;

/**
 * Broadcast Extension Information File
 */
class Extension extends BaseExtension
{
    public function register()
    {
        parent::register();

        Manager::register($this->app);
    }

    public function boot()
    {
        Manager::boot($this->app);
    }

    public function registerSettings(): array
    {
        return [
            'settings' => [
                'label' => 'Broadcast Settings',
                'description' => 'Manage pusher api and cluster settings.',
                'icon' => 'fa fa-bullhorn',
                'model' => \Igniter\Broadcast\Models\Settings::class,
            ],
        ];
    }

    public function registerEventBroadcasts()
    {
        return [];
    }
}

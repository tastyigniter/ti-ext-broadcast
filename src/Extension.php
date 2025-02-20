<?php

declare(strict_types=1);

namespace Igniter\Broadcast;

use Igniter\Broadcast\Classes\Manager;
use Igniter\Broadcast\Models\Settings;
use Igniter\System\Classes\BaseExtension;

/**
 * Broadcast Extension Information File
 */
class Extension extends BaseExtension
{
    public function register(): void
    {
        parent::register();

        Manager::register($this->app);
    }

    public function boot(): void
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
                'model' => Settings::class,
            ],
        ];
    }

    public function registerEventBroadcasts(): array
    {
        return [];
    }
}

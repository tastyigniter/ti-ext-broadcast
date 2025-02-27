<?php

declare(strict_types=1);

namespace Igniter\Broadcast;

use Override;
use Igniter\Broadcast\Classes\Manager;
use Igniter\Broadcast\Models\Settings;
use Igniter\System\Classes\BaseExtension;

/**
 * Broadcast Extension Information File
 */
class Extension extends BaseExtension
{
    #[Override]
    public function register(): void
    {
        parent::register();

        Manager::register($this->app);
    }

    #[Override]
    public function boot(): void
    {
        Manager::boot($this->app);
    }

    #[Override]
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

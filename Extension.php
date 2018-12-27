<?php namespace Igniter\Pusher;

use System\Classes\BaseExtension;

/**
 * Pusher Extension Information File
 */
class Extension extends BaseExtension
{
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
                'model' => 'Igniter\Pusher\Models\Settings'
            ],
        ];
    }
}

<?php

namespace Igniter\Broadcast\Models;

use Model;
use System\Classes\ExtensionManager;

class Settings extends Model
{
    public $implement = ['System\Actions\SettingsModel'];

    // A unique code
    public $settingsCode = 'igniter_broadcast_settings';

    // Reference to field configuration
    public $settingsFieldsConfig = 'settings';

    public static function findRegisteredBroadcasts()
    {
        $results = [];
        $broadcastBundle = ExtensionManager::instance()->getRegistrationMethodValues('registerEventBroadcasts');

        foreach ($broadcastBundle as $extension => $broadcasts) {
            foreach ($broadcasts as $event => $broadcast) {
                $results[$event] = $broadcast;
            }
        }

        return $results;
    }

    public static function findEventBroadcasts()
    {
        $results = [];
        foreach (self::findRegisteredBroadcasts() as $eventCode => $broadcastClass) {
            $results[$eventCode] = $broadcastClass;
        }

        return $results;
    }
}

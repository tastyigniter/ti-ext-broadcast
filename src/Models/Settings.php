<?php

namespace Igniter\Broadcast\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Igniter;
use Igniter\System\Classes\ExtensionManager;

class Settings extends Model
{
    public array $implement = [\Igniter\System\Actions\SettingsModel::class];

    // A unique code
    public string $settingsCode = 'igniter_broadcast_settings';

    // Reference to field configuration
    public string $settingsFieldsConfig = 'settings';

    public static function isConfigured()
    {
        return Igniter::hasDatabase()
            && strlen(self::get('app_id'))
            && strlen(self::get('key'))
            && strlen(self::get('secret'));
    }

    public static function findRegisteredBroadcasts()
    {
        $results = [];
        $broadcastBundle = resolve(ExtensionManager::class)->getRegistrationMethodValues('registerEventBroadcasts');

        foreach ($broadcastBundle as $broadcasts) {
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

    public static function useWebsockets()
    {
        return false;
    }
}

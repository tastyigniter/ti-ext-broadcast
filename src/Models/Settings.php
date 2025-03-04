<?php

declare(strict_types=1);

namespace Igniter\Broadcast\Models;

use Igniter\Flame\Database\Model;
use Igniter\Flame\Support\Facades\Igniter;
use Igniter\System\Actions\SettingsModel;
use Igniter\System\Classes\ExtensionManager;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool set(string|array $key, mixed $value)
 * @mixin SettingsModel
 */
class Settings extends Model
{
    public array $implement = [SettingsModel::class];

    // A unique code
    public string $settingsCode = 'igniter_broadcast_settings';

    // Reference to field configuration
    public string $settingsFieldsConfig = 'settings';

    public static function isConfigured(): bool
    {
        return Igniter::hasDatabase()
            && strlen((string)self::get('app_id'))
            && strlen((string)self::get('key'))
            && strlen((string)self::get('secret'));
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

    public static function useWebsockets(): bool
    {
        return false;
    }
}

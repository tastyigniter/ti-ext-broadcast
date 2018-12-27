<?php

namespace Igniter\Pusher\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System\Actions\SettingsModel'];

    // A unique code
    public $settingsCode = 'igniter_pusher_settings';

    // Reference to field configuration
    public $settingsFieldsConfig = 'settings';
}

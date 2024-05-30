<?php

namespace Igniter\Automation\Tests;

use Igniter\Broadcast\Models\Settings;
use Illuminate\Broadcasting\BroadcastManager;

beforeEach(function() {
    Settings::clearInternalCache();
});

it('sets configuration values from settings model correctly', function() {
    Settings::set('driver', 'pusher');
    Settings::set('app_id', '123');
    Settings::set('key', '123');
    Settings::set('secret', '123');

    resolve(BroadcastManager::class);

    expect(Settings::get('driver', 'pusher'))->toEqual(config('broadcasting.default'))
        ->and(Settings::get('key'))->toEqual(config('broadcasting.connections.pusher.key'))
        ->and(Settings::get('secret'))->toEqual(config('broadcasting.connections.pusher.secret'))
        ->and(Settings::get('app_id'))->toEqual(config('broadcasting.connections.pusher.app_id'))
        ->and(Settings::get('cluster'))->toEqual(config('broadcasting.connections.pusher.options.cluster'))
        ->and(Settings::get('encrypted'))->toEqual(config('broadcasting.connections.pusher.options.encrypted'));
});

it('returns true if the settings model is configured', function() {
    expect(Settings::isConfigured())->toBeFalse();

    Settings::set('app_id', '123');
    Settings::set('key', '123');
    Settings::set('secret', '123');

    expect(Settings::isConfigured())->toBeTrue();
});


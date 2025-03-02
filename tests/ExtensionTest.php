<?php

declare(strict_types=1);

namespace Igniter\Automation\Tests;

use Igniter\Broadcast\Extension;
use Igniter\Broadcast\Models\Settings;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Contracts\Foundation\Application;
use Mockery;

beforeEach(function(): void {
    Settings::clearInternalCache();
});

it('sets configuration values from settings model correctly', function(): void {
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

it('returns true if the settings model is configured', function(): void {
    expect(Settings::isConfigured())->toBeFalse();

    Settings::set('app_id', '123');
    Settings::set('key', '123');
    Settings::set('secret', '123');

    expect(Settings::isConfigured())->toBeTrue();
});

it('returns correct settings array', function(): void {
    $extension = new Extension(Mockery::mock(Application::class));
    $settings = $extension->registerSettings();

    expect($settings)->toHaveKey('settings')
        ->and($settings['settings']['label'])->toBe('Broadcast Settings')
        ->and($settings['settings']['description'])->toBe('Manage pusher api and cluster settings.')
        ->and($settings['settings']['icon'])->toBe('fa fa-bullhorn')
        ->and($settings['settings']['model'])->toBe(Settings::class);
});

it('returns empty array for event broadcasts', function(): void {
    $extension = new Extension(Mockery::mock(Application::class));
    $eventBroadcasts = $extension->registerEventBroadcasts();

    expect($eventBroadcasts)->toBe([]);
});

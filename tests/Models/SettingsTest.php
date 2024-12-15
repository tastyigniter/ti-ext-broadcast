<?php

namespace Igniter\Broadcast\Tests\Models;

use Igniter\Broadcast\Models\Settings;
use Igniter\Broadcast\Tests\TestBroadcastEvent;
use Igniter\System\Actions\SettingsModel;
use Igniter\System\Classes\ExtensionManager;
use Mockery;

it('finds registered broadcasts', function() {
    $extensionManager = Mockery::mock(ExtensionManager::class);
    $extensionManager->shouldReceive('getRegistrationMethodValues')->with('registerEventBroadcasts')->andReturn([
        [
            'event1' => TestBroadcastEvent::class,
            'event2' => TestBroadcastEvent::class,
        ],
    ])->once();
    app()->instance(ExtensionManager::class, $extensionManager);
    $broadcasts = Settings::findRegisteredBroadcasts();

    expect($broadcasts)->toBeArray()
        ->and($broadcasts)->toHaveKey('event1')
        ->and($broadcasts['event1'])->toEqual(TestBroadcastEvent::class)
        ->and($broadcasts)->toHaveKey('event2')
        ->and($broadcasts['event2'])->toEqual(TestBroadcastEvent::class);
});

it('finds event broadcasts', function() {
    $extensionManager = Mockery::mock(ExtensionManager::class);
    $extensionManager->shouldReceive('getRegistrationMethodValues')->with('registerEventBroadcasts')->andReturn([
        [
            'event1' => TestBroadcastEvent::class,
            'event2' => TestBroadcastEvent::class,
        ],
    ])->once();
    app()->instance(ExtensionManager::class, $extensionManager);

    $broadcasts = Settings::findEventBroadcasts();

    expect($broadcasts)->toBeArray()
        ->and($broadcasts)->toHaveKey('event1')
        ->and($broadcasts['event1'])->toEqual(TestBroadcastEvent::class)
        ->and($broadcasts)->toHaveKey('event2')
        ->and($broadcasts['event2'])->toEqual(TestBroadcastEvent::class);
});

it('checks if websockets are enabled', function() {
    expect(Settings::useWebsockets())->toBeFalse();
});

it('configures settings model correctly', function() {
    $settings = new Settings;

    expect($settings->implement)->toContain(SettingsModel::class)
        ->and($settings->settingsCode)->toBe('igniter_broadcast_settings')
        ->and($settings->settingsFieldsConfig)->toEqual('settings');
});

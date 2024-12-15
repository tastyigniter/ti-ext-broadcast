<?php

namespace Igniter\Broadcast\Tests\Classes;

use Igniter\Admin\Classes\AdminController;
use Igniter\Broadcast\Classes\Manager;
use Igniter\Broadcast\Models\Settings;
use Igniter\Broadcast\Tests\TestBroadcastEvent;
use Igniter\Main\Classes\MainController;
use Igniter\System\Classes\ExtensionManager;
use Igniter\System\Facades\Assets;
use Igniter\User\Facades\AdminAuth;
use Igniter\User\Facades\Auth;
use Igniter\User\Models\Customer;
use Igniter\User\Models\User;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use Mockery;
use ReflectionClass;

function callProtectedMethod($class, string $methodName, array $args = []): mixed
{
    $reflection = new ReflectionClass($class);
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);
    return $method->invokeArgs($class, $args);
}

it('binds broadcasts for given events', function() {
    Event::shouldReceive('listen')->twice();

    Manager::bindBroadcasts([
        'event1' => 'BroadcastClass1',
        'event2' => 'BroadcastClass2',
    ]);
});

it('binds a single broadcast event', function() {
    Event::shouldReceive('listen')->with('event1', Mockery::on(function($callback) {
        $callback();
        return true;
    }))->once();
    Event::shouldReceive('dispatch')->once();

    Manager::bindBroadcast('event1', TestBroadcastEvent::class);
});

it('registers broadcast settings in application config', function() {
    $app = Mockery::mock(Application::class);
    $app->config = Mockery::mock();
    $app->config->shouldReceive('set')->times(7);
    $app->shouldReceive('resolving')->with(BroadcastManager::class, Mockery::on(function($callback) {
        $callback();
        return true;
    }))->once();

    Settings::set([
        'secret' => 'secret',
        'app_id' => '123',
        'key' => '123',
    ]);

    Manager::register($app);
});

it('boots and binds broadcasts if configured', function() {
    $app = Mockery::mock(Application::class);
    $app->shouldReceive('providerIsLoaded')->andReturn(false);
    Settings::set([
        'secret' => 'secret',
        'app_id' => '123',
        'key' => '123',
    ]);
    $extensionManager = Mockery::mock(ExtensionManager::class);
    $extensionManager->shouldReceive('getRegistrationMethodValues')->with('registerEventBroadcasts')->andReturn([
        [
            'event1' => TestBroadcastEvent::class,
        ],
    ])->once();
    app()->instance(ExtensionManager::class, $extensionManager);

    Broadcast::shouldReceive('routes')->once();
    Broadcast::shouldReceive('channel')->with('admin.users.{userId}', Mockery::on(function($callback) {
        $user = Mockery::mock(User::class)->makePartial();
        $callback($user, 1);
        return true;
    }), ['guards' => ['web', 'igniter-admin']]);
    Broadcast::shouldReceive('channel')->with('main.users.{userId}', Mockery::on(function($callback) {
        $customer = Mockery::mock(Customer::class)->makePartial();
        $callback($customer, 1);
        return true;
    }), ['guards' => ['web', 'igniter-customer']]);

    Manager::boot($app);

    AdminController::extend(function(AdminController $controller) {
        $controller->fireEvent('controller.beforeRemap');
    });

    MainController::extend(function(MainController $controller) {
        $controller->fireEvent('controller.beforeRemap');
    });
});

it('does not boot if not configured', function() {
    Settings::clearInternalCache();
    $app = Mockery::mock(Application::class);

    Event::shouldReceive('dispatch');
    Event::shouldReceive('listen')->never();
    Broadcast::shouldReceive('routes')->never();
    Broadcast::shouldReceive('channel')->never();

    Manager::boot($app);
});

it('dispatches broadcast event', function() {
    $broadcastClass = TestBroadcastEvent::class;
    $params = ['user' => null];

    Event::shouldReceive('dispatch')->once()->with(Mockery::type($broadcastClass));

    Manager::dispatch($broadcastClass, $params);
});

it('throws exception if broadcast class does not exist', function() {
    $broadcastClass = 'NonExistentClass';
    $params = ['param1', 'param2'];

    expect(fn() => Manager::dispatch($broadcastClass, $params))
        ->toThrow(InvalidArgumentException::class);
});

it('adds assets to admin controller', function() {
    $controller = new AdminController;
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('receivesBroadcastNotificationsOn')->andReturn('channel');
    AdminAuth::shouldReceive('isLogged')->andReturn(true);
    AdminAuth::shouldReceive('user')->andReturn($user);

    Assets::shouldReceive('putJsVars')->once();
    Assets::shouldReceive('addJs')->times(3);

    callProtectedMethod(new Manager, 'addAssetsToController', [$controller]);
});

it('adds assets to main controller', function() {
    $controller = new MainController;
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->shouldReceive('receivesBroadcastNotificationsOn')->andReturn('channel');
    Auth::shouldReceive('isLogged')->andReturn(true);
    Auth::shouldReceive('user')->andReturn($customer);

    Assets::shouldReceive('putJsVars')->once();
    Assets::shouldReceive('addJs')->times(3);

    callProtectedMethod(new Manager, 'addAssetsToController', [$controller]);
});

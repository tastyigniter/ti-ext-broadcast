<?php

namespace Igniter\Broadcast\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Igniter\Flame\ServiceProvider::class,
            \Igniter\Broadcast\Extension::class,
        ];
    }
}

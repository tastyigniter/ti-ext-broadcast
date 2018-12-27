<?php

namespace Igniter\Pusher\Components;

use System\Classes\BaseComponent;

class Pusher extends BaseComponent
{
    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->addJs('~/extensions/igniter/pusher/assets/js/pusher.js');
    }
}

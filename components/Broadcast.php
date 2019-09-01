<?php

namespace Igniter\Pusher\Components;

use System\Classes\BaseComponent;

class Broadcast extends BaseComponent
{
    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $this->addJs('~/extensions/igniter/broadcast/assets/js/broadcast.js');
    }
}

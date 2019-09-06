<?php

namespace Igniter\Broadcast\Components;

use System\Classes\BaseComponent;

class Broadcast extends BaseComponent
{
    public function onRun()
    {
        $this->addJs('~/extensions/igniter/broadcast/assets/js/broadcast.js');
    }
}

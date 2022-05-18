<?php

namespace Igniter\Broadcast\Components;

use Igniter\System\Classes\BaseComponent;

class Broadcast extends BaseComponent
{
    public function onRun()
    {
        $this->addJs('$/igniter/broadcast/assets/js/broadcast.js');
    }
}

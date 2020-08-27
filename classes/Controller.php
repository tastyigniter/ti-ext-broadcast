<?php

namespace Igniter\Broadcast\Classes;

use Broadcast;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Authenticate the request for channel access.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function auth(Request $request)
    {
        return Broadcast::auth($request);
    }
}

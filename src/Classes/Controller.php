<?php

namespace Igniter\Broadcast\Classes;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Broadcast;

class Controller extends BaseController
{
    /**
     * Authenticate the request for channel access.
     *
     * @return \Illuminate\Http\Response
     */
    public function auth(Request $request)
    {
        return Broadcast::auth($request);
    }
}

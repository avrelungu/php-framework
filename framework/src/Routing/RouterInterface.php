<?php

namespace AurelLungu\Framework\Routing;

use AurelLungu\Framework\Http\Request;

interface RouterInterface
{
    public function dispatch(Request $request);
}
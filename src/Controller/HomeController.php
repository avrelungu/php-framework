<?php

namespace App\Controller;

use App\Widget;
use AurelLungu\Framework\Http\Response;

class HomeController
{
    public function __construct(private Widget $widget)
    {
        
    }

    public function index(): Response {
        return new Response('avrel a facut si singur');
    }
}

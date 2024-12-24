<?php

namespace App\Controller;

use AurelLungu\Framework\Http\Response;

class HomeController
{
    public function index(): Response {
        return new Response('avrel a facut si singur');
    }
}

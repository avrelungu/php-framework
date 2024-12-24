<?php

namespace App\Controller;

use AurelLungu\Framework\Http\Response;

class PostController
{
    public function show(int $id): Response
    {
        return new Response($id);
    }
}
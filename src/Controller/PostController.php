<?php

namespace App\Controller;

use AurelLungu\Framework\Controller\AbstractController;
use AurelLungu\Framework\Http\Response;

class PostController extends AbstractController
{
    public function show(int $id): Response
    {
        return $this->render('posts.html.twig', [
            'postId' => $id
        ]);
    }

    public function create(): Response
    {
        return $this->render('create-post.html.twig');
    }
}
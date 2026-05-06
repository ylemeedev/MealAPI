<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DebugController extends AbstractController
{
    #[Route('/api/debug-me', name: 'debug_me')]
    public function debug(Request $request, Security $security): Response
    {
        //dd($request->headers->all());

        return $this->json([
            'authorization_header' => $request->headers->get('Authorization'),
            'server_http_auth' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
        ]);
    }
}

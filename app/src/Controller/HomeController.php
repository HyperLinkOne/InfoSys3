<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Response;
use App\Service\AuthService;
use Twig\Environment;

class HomeController extends BaseController
{
    public function __construct(
        private AuthService $authService,
        private Environment $twig,
    ) {
    }

    public function index(): Response
    {
        return new Response(
            $this->twig->render('home.html.twig', [
                'isLoggedIn' => $this->authService->isLoggedIn(),
                'isAdmin' => $this->authService->isAdmin()
            ])
        );
    }
}

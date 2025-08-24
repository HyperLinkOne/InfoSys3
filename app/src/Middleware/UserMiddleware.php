<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Service\AuthService;
use App\Service\ConfigService;
use App\Service\UserService;
use Twig\Environment;

class UserMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AuthService $authService,
        private UserService $userService,
        private Environment $twig,
        private ConfigService $configService,
    ) {
    }

    public function handle(Request $request, callable $next): Response
    {
        // Set current user in Twig globals
        if ($this->authService->isLoggedIn()) {
            $userId = $this->authService->getCurrentUserId();
            $user = $this->userService->getUser($userId);
            $this->twig->addGlobal('app', [
                'env' => $this->configService->get('app.env'),
                'name' => $this->configService->get('app.name'),
                'isProduction' => $this->configService->isProduction(),
                'user' => $user
            ]);
        } else {
            $this->twig->addGlobal('app', [
                'env' => $this->configService->get('app.env'),
                'name' => $this->configService->get('app.name'),
                'isProduction' => $this->configService->isProduction(),
                'user' => null
            ]);
        }

        return $next($request);
    }
}

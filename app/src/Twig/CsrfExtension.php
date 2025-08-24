<?php

declare(strict_types=1);

namespace App\Twig;

use App\Security\CsrfTokenManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    public function __construct(private CsrfTokenManager $csrfManager) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_token', [$this, 'generateToken']),
        ];
    }

    public function generateToken(string $id = 'default'): string
    {
        return $this->csrfManager->generateToken($id);
    }
}

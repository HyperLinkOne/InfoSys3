<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\ConfigService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\Security\CsrfTokenManager;

class TwigFactory
{
    public static function create(string $templatesPath, ?string $cachePath, ConfigService $config): Environment
    {
        $loader = new FilesystemLoader($templatesPath);

        $options = [
            'debug' => $config->isDebug(),
            'cache' => $config->isDebug() ? false : $cachePath, // Nur Cache im Prod-Modus
            'strict_variables' => true,
        ];

        $twig = new Environment($loader, $options);

        // Debug-Extension nur in Dev
        if ($config->isDebug()) {
            $twig->enableDebug();
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        // CSRF-Extension hinzufügen
        $csrfManager = new CsrfTokenManager();
        $twig->addExtension(new CsrfExtension($csrfManager));

        // Globale Variablen (z. B. APP_ENV, APP_NAME)
        $twig->addGlobal('app', [
            'env' => $config->get('app.env'),
            'name' => $config->get('app.name'),
            'user' => null // Will be set by middleware
        ]);

        return $twig;
    }
}

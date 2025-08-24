<?php

declare(strict_types=1);

// Start session at the very beginning, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use App\Controller\BlogController;
use App\Controller\HomeController;
use App\Controller\UserController;
use App\Core\Container;
use App\Http\JsonResponse;
use App\Http\Request;
use App\Http\Router;
use App\Middleware\CorsMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\ExceptionMiddleware;
use App\Middleware\LoggingMiddleware;
use App\Middleware\UserMiddleware;
use App\Repository\BlogPostRepository;
use App\Repository\UserRepository;
use App\Security\CsrfTokenManager;
use App\Service\AuthService;
use App\Service\BlogService;
use App\Service\ConfigService;
use App\Service\DatabaseService;
use App\Service\UserService;
use App\Twig\TwigFactory;

use Dotenv\Dotenv;
use Twig\Environment;


require dirname(__DIR__) . '/vendor/autoload.php';


// .env laden
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

set_exception_handler(function(Throwable $e) {
    $isDev = $_ENV['APP_ENV'] === 'dev';
    $data = ['error' => 'Server Error'];

    if ($isDev) {
        $data['message'] = $e->getMessage();
        $data['trace'] = $e->getTraceAsString();
    }

    (new JsonResponse($data, 500))->send();
});

$config = new ConfigService();
$twig = TwigFactory::create(
    __DIR__ . '/../templates',
    __DIR__ . '/../var/cache/twig',
    $config
);

//$container = require __DIR__ . '/../src/Core/Container.php'; // wo du deinen DI aufsetzt
$container = new Container();
$container->set(ConfigService::class, fn() => new ConfigService());
$container->set(DatabaseService::class, fn($c) => new DatabaseService($c->get(ConfigService::class)));
$container->set(TwigFactory::class, fn() => new TwigFactory());
$container->set(CsrfTokenManager::class, fn() => new CsrfTokenManager());
$container->set(Environment::class, fn() => $twig);

$container->set(BlogPostRepository::class, function ($c) {
    return new BlogPostRepository($c->get(DatabaseService::class));
});
$container->set(BlogService::class, function ($c) {
    return new BlogService($c->get(BlogPostRepository::class));
});

$container->set(BlogController::class, function ($c) {
    return new BlogController(
        $c->get(BlogService::class),
        $c->get(AuthService::class),
        $c->get(CsrfTokenManager::class),
        $c->get(Environment::class)
    );
});

$container->set(HomeController::class, function ($c) {
    return new HomeController(
        $c->get(AuthService::class),
        $c->get(Environment::class)
    );
});

// User management services
$container->set(UserRepository::class, function ($c) {
    return new UserRepository($c->get(DatabaseService::class));
});
$container->set(UserService::class, function ($c) {
    return new UserService($c->get(UserRepository::class));
});
$container->set(AuthService::class, fn() => new AuthService());

$container->set(UserController::class, function ($c) {
    return new UserController(
        $c->get(UserService::class),
        $c->get(AuthService::class),
        $c->get(CsrfTokenManager::class),
        $c->get(Environment::class)
    );
});


// add all Services

$router = new Router($container);

// Middlewares registrieren
$router->addMiddleware(new CorsMiddleware());
$router->addMiddleware(new LoggingMiddleware());
$router->addMiddleware(new ExceptionMiddleware(true));
$csrfManager = new CsrfTokenManager();
$router->addMiddleware(new CsrfMiddleware(($csrfManager)));

// Add UserMiddleware to set current user in templates
$router->addMiddleware(new UserMiddleware(
    $container->get(AuthService::class),
    $container->get(UserService::class),
    $twig,
    $container->get(ConfigService::class),
));

// Routen laden
require dirname(__DIR__) . '/config/routes.php';

// Dispatch
$request = new Request();
$router->dispatch($request);

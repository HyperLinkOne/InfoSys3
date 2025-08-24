<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\ContainerInterface;
use App\Middleware\MiddlewareInterface;


class Router
{
    /** @var array<int, array{pattern:string, handler:mixed, methods:array<int,string>, middlewares:array<int,MiddlewareInterface>}> */
    private array $routes = [];
    private array $middlewares = [];
    private array $namedRoutes = [];

    public function __construct(
        private ContainerInterface $container,
    ){
    }

    /**
     * Registriert eine neue Route
     * @param string|array $methods   z.B. 'GET' oder ['GET','POST'] (Groß-/Kleinschreibung egal)
     * @param string $path      z.B. '/api/items/{id}'
     * @param callable|array $handler z.B. [Controller::class, 'action'] oder fn()=>...
     * @param MiddlewareInterface[] $middlewares nur für diese Route
     * @param ?string $name für benannte routen
     */
    public function add(string|array $methods, string $path, callable|array $handler, array $middlewares = [], ?string $name = null): void
    {
        $methods = array_map('strtoupper', (array)$methods);

        // Wandelt {id} in Named Capturing Group um
        $pattern = preg_replace('#\{([\w]+)\}#', '(?P<$1>[^/]+)', rtrim($path, '/'));
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'pattern'     => $pattern,
            'path'        => $path,
            'handler'     => $handler,
            'methods'     => $methods,
            'middlewares' => $middlewares,
        ];

        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
    }

    /** Globale Middleware hinzufügen (läuft für alle Routen) */
    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Generiert eine URL anhand des Routen-Namens
     */
    public function generateUrl(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \InvalidArgumentException("Route '$name' not found");
        }
        $path = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }
        return $path;
    }

    /**
     * Verarbeitet die eingehende Anfrage
     */
    public function dispatch(Request $request): void
    {
        $uri = rtrim($request->uri(), '/');
        $method = $request->method();

        $allowedMethods = [];

        foreach ($this->routes as $route) {
            // todo: check if early fail is possible
//            if (!in_array($method, $route['methods'], true)) {
//                continue;
//            }
//            if (!preg_match($route['pattern'], $uri, $matches)) {
//                continue;
//            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                $allowedMethods = array_merge($allowedMethods, $route['methods']);

                if (in_array($method, $route['methods'], true)) {
                    // Benannte Parameter extrahieren
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $handler = $route['handler'];

                    // Controller-Handler
                    $controllerHandler = function (Request $req) use ($handler, $params) {
                        if (is_array($handler)) {
                            [$class, $action] = $handler;
                            $controller = $this->container->get($class);
                            $raw = call_user_func_array([$controller, $action], [$req, ...$params]);
                            return $this->normalizeResponse($raw);
                        }
                        $raw = call_user_func_array($handler, [$req, ...$params]);
                        return $this->normalizeResponse($raw);
                    };

                    // Pipeline: global + route middlewares
                    $pipelineMiddlewares = array_merge($this->middlewares, $route['middlewares']);
                    $pipeline = array_reduce(
                        array_reverse($pipelineMiddlewares),
                        fn($next, $middleware) => fn($req) => $middleware->handle($req, $next),
                        $controllerHandler
                    );

                    $rawResponse = $pipeline($request);
                    $response = $this->normalizeResponse($rawResponse);
                    $response->send();
                    return;
                }
            }
        }

        // Fehlerbehandlung
        if (!empty($allowedMethods)) {
            (new JsonResponse(['error' => 'Method Not Allowed'], 405, ['Allow' => implode(', ', $allowedMethods)]))->send();
        } else {
            (new JsonResponse(['error' => 'Route not found'], 404))->send();
        }
    }

    private function normalizeResponse(mixed $response): Response
    {
        if ($response instanceof Response) {
            return $response;
        }

        if (is_array($response)) {
            return new JsonResponse($response);
        }

        return new HtmlResponse((string)$response);
    }
}

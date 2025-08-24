<?php

use App\Controller\BlogController;
use App\Controller\HomeController;
use App\Controller\UserController;

/** Home */
$router->add('GET', '/', [HomeController::class, 'index']);

/** BlogPosts */
$router->add('GET', '/blog', [BlogController::class, 'index']);
$router->add(['GET', 'POST'], '/blog/create', [BlogController::class, 'create']);
$router->add(['GET', 'POST'], '/blog/{id}/edit', [BlogController::class, 'edit']);
$router->add(['POST'], '/blog/{id}/delete', [BlogController::class, 'delete']);
$router->add('GET', '/blog/{id}', [BlogController::class, 'show']);

/** User Management */
$router->add(['GET', 'POST'], '/login', [UserController::class, 'login']);
$router->add('GET', '/logout', [UserController::class, 'logout']);
$router->add('GET', '/profile', [UserController::class, 'profile']);
$router->add(['GET', 'POST'], '/profile', [UserController::class, 'profile']);

// Admin routes
$router->add('GET', '/users', [UserController::class, 'index']);
$router->add(['GET', 'POST'], '/users/create', [UserController::class, 'create']);
$router->add('GET', '/users/{id}', [UserController::class, 'show']);
$router->add(['GET', 'POST'], '/users/{id}/edit', [UserController::class, 'edit']);
$router->add(['POST'], '/users/{id}/delete', [UserController::class, 'delete']);

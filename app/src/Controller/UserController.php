<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Http\Response;
use App\Security\CsrfTokenManager;
use App\Service\AuthService;
use App\Service\UserService;
use Twig\Environment;

class UserController extends BaseController
{
    public function __construct(
        private UserService $userService,
        private AuthService $authService,
        private CsrfTokenManager $csrf,
        private Environment $twig,
    ){
    }

    public function index(): Response
    {
        $this->authService->requireAdmin();
        
        $users = $this->userService->getAllUsers();

        return new Response(
            $this->twig->render('user/index.html.twig', [
                'users' => $users,
                'csrf_token' => $this->csrf->generateToken('default')
            ])
        );
    }

    public function show(Request $request, int $id): Response
    {
        $this->authService->requireAuth();
        
        $user = $this->userService->getUser($id);
        if (!$user) {
            return new Response('User not found', 404);
        }

        // Users can only view their own profile unless they're admin
        if (!$this->authService->isAdmin() && $this->authService->getCurrentUserId() !== $id) {
            return new Response('Access denied', 403);
        }

        return new Response(
            $this->twig->render('user/show.html.twig', [
                'user' => $user,
                'csrf_token' => $this->csrf->generateToken('default')
            ])
        );
    }

    public function create(Request $request): Response
    {
        $this->authService->requireAdmin();

        if ($request->method() === 'POST') {
            $token = $request->input('_csrf_token');
            if (!$this->csrf->isTokenValid('default', $token)) {
                return new Response('Invalid CSRF token', 400);
            }

            $username = $request->input('username');
            $email = $request->input('email');
            $password = $request->input('password');
            $role = $request->input('role', 'user');
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');

            try {
                $this->userService->createUser($username, $email, $password, $role, $firstName, $lastName);
                return new Response('', 302, ['Location' => '/users']);
            } catch (\InvalidArgumentException $e) {
                return new Response(
                    $this->twig->render('user/create.html.twig', [
                        'error' => $e->getMessage(),
                        'form_data' => [
                            'username' => $username,
                            'email' => $email,
                            'role' => $role,
                            'first_name' => $firstName,
                            'last_name' => $lastName
                        ]
                    ])
                );
            }
        }

        return new Response($this->twig->render('user/create.html.twig'));
    }

    public function edit(Request $request, int $id): Response
    {
        $this->authService->requireAuth();
        
        $user = $this->userService->getUser($id);
        if (!$user) {
            return new Response('User not found', 404);
        }

        // Users can only edit their own profile unless they're admin
        if (!$this->authService->isAdmin() && $this->authService->getCurrentUserId() !== $id) {
            return new Response('Access denied', 403);
        }

        if ($request->method() === 'POST') {
            $token = $request->input('_csrf_token');
            if (!$this->csrf->isTokenValid('default', $token)) {
                return new Response('Invalid CSRF token', 400);
            }

            $username = $request->input('username');
            $email = $request->input('email');
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $role = $request->input('role', 'user');
            $isActive = $request->input('is_active') === '1';

            try {
                $this->userService->updateUser($id, $username, $email, $firstName, $lastName, $role, $isActive);
                return new Response('', 302, ['Location' => '/users/' . $id]);
            } catch (\InvalidArgumentException $e) {
                return new Response(
                    $this->twig->render('user/edit.html.twig', [
                        'user' => $user,
                        'error' => $e->getMessage()
                    ])
                );
            }
        }

        return new Response(
            $this->twig->render('user/edit.html.twig', [
                'user' => $user
            ])
        );
    }

    public function delete(Request $request, int $id): Response
    {
        $this->authService->requireAdmin();

        if ($request->method() !== 'POST') {
            return new JsonResponse(['error' => 'Method Not Allowed'], 405);
        }

        $token = $request->input('_csrf_token');
        if (!$this->csrf->isTokenValid('default', $token)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 400);
        }

        $user = $this->userService->getUser($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Prevent admin from deleting themselves
        if ($this->authService->getCurrentUserId() === $id) {
            return new JsonResponse(['error' => 'Cannot delete your own account'], 400);
        }

        $this->userService->deleteUser($id);
        return new Response('', 302, ['Location' => '/users']);
    }

    public function login(Request $request): Response
    {
        if ($this->authService->isLoggedIn()) {
            return new Response('', 302, ['Location' => '/']);
        }

        if ($request->method() === 'POST') {
            $token = $request->input('_csrf_token');
            if (!$this->csrf->isTokenValid('default', $token)) {
                return new Response('Invalid CSRF token', 400);
            }

            $username = $request->input('username');
            $password = $request->input('password');

            $user = $this->userService->authenticateUser($username, $password);
            
            if ($user) {
                $this->authService->login($user);
                return new Response('', 302, ['Location' => '/']);
            } else {
                return new Response(
                    $this->twig->render('user/login.html.twig', [
                        'error' => 'Invalid username or password'
                    ])
                );
            }
        }

        return new Response($this->twig->render('user/login.html.twig'));
    }

    public function logout(): Response
    {
        $this->authService->logout();
        return new Response('', 302, ['Location' => '/login']);
    }

    public function profile(Request $request): Response
    {
        $this->authService->requireAuth();
        
        $userId = $this->authService->getCurrentUserId();
        $user = $this->userService->getUser($userId);

        if ($request->method() === 'POST') {
            $token = $request->input('_csrf_token');
            if (!$this->csrf->isTokenValid('default', $token)) {
                return new Response('Invalid CSRF token', 400);
            }

            $currentPassword = $request->input('current_password');
            $newPassword = $request->input('new_password');
            $confirmPassword = $request->input('confirm_password');

            if ($newPassword !== $confirmPassword) {
                return new Response(
                    $this->twig->render('user/profile.html.twig', [
                        'user' => $user,
                        'error' => 'New passwords do not match'
                    ])
                );
            }

            if (!$this->userService->authenticateUser($user->getUsername(), $currentPassword)) {
                return new Response(
                    $this->twig->render('user/profile.html.twig', [
                        'user' => $user,
                        'error' => 'Current password is incorrect'
                    ])
                );
            }

            try {
                $this->userService->updatePassword($userId, $newPassword);
                return new Response(
                    $this->twig->render('user/profile.html.twig', [
                        'user' => $user,
                        'success' => 'Password updated successfully'
                    ])
                );
            } catch (\InvalidArgumentException $e) {
                return new Response(
                    $this->twig->render('user/profile.html.twig', [
                        'user' => $user,
                        'error' => $e->getMessage()
                    ])
                );
            }
        }

        return new Response(
            $this->twig->render('user/profile.html.twig', [
                'user' => $user
            ])
        );
    }
}

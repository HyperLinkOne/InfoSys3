<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\JsonResponse;
use App\Http\Request;
use App\Http\Response;
use App\Security\CsrfTokenManager;
use App\Service\AuthService;
use App\Service\BlogService;
use Twig\Environment;

class BlogController extends BaseController
{
    public function __construct(
        private BlogService $blogService,
        private AuthService $authService,
        private CsrfTokenManager $csrf,
        private Environment $twig,
    ){
    }

    public function index(): Response
    {
        $posts = $this->blogService->getAllPosts();
         $template = $this->authService->isLoggedIn() ? 'blog/index.html.twig' : 'blog/index.view.html.twig';

         return new Response(
            $this->twig->render($template, [
                'posts' => $posts,
                'csrf_token' => $this->csrf->generateToken('default')
            ])
        );
    }

    public function show(Request $request, int $id): Response
    {
        $this->authService->requireAuth();
        
        $post = $this->blogService->getPost($id);
        if (!$post) {
            return new Response('Post not found', 404);
        }
        return new Response(
            $this->twig->render('blog/show.html.twig', [
                'post' => $post,
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

            $title = $request->input('title');
            $content = $request->input('content');

            try {
                $this->blogService->createPost($title, $content);
                return new Response('', 302, ['Location' => '/blog']);
            } catch (\InvalidArgumentException $e) {
                return new Response(
                    $this->twig->render('blog/create.html.twig', [
                        'error' => $e->getMessage(),
                        'form_data' => [
                            'title' => $title,
                            'content' => $content,
                        ]
                    ])
                );
            }
        }

        return new Response($this->twig->render('blog/create.html.twig'));
    }

    public function edit(Request $request, int $id): Response
    {
        $post = $this->blogService->getPost($id);
        if (!$post) {
            return new Response('Post not found', 404);
        }

        if ($request->method() === 'POST') {
            $token = $request->input('_csrf_token');
            if (!$this->csrf->isTokenValid('default', $token)) {
                return new Response('Invalid CSRF token', 400);
            }

            $title = $request->input('title');
            $content = $request->input('content');
            
            if (empty($title) || empty($content)) {
                return new Response(
                    $this->twig->render('blog/edit.html.twig', [
                        'post' => $post,
                        'error' => 'Title and content are required'
                    ])
                );
            }

            $this->blogService->updatePost($id, $title, $content);
            return new Response('', 302, ['Location' => '/blog/' . $id]);
        }

        return new Response(
            $this->twig->render('blog/edit.html.twig', [
                'post' => $post
            ])
        );
    }

    public function delete(Request $request, int $id): Response
    {
        $this->authService->requireAuth();

        if ($request->method() !== 'POST') {
            return new JsonResponse(['error' => 'Method Not Allowed'], 405);
        }

        $token = $request->input('_csrf_token');
        if (!$this->csrf->isTokenValid('default', $token)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 400);
        }

        $post = $this->blogService->getPost($id);
        if (!$post) {
            return new JsonResponse(['error' => 'Post not found'], 404);
        }

        $this->blogService->deletePost($id);

        return new Response('', 302, ['Location' => '/blog']);
    }
}

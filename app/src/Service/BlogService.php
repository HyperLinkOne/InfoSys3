<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\BlogPostRepository;

class BlogService
{
    private BlogPostRepository $repository;

    public function __construct(BlogPostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPosts(): array
    {
        return $this->repository->findAll();
    }

    public function getPost(int $id)
    {
        return $this->repository->find($id);
    }

    public function createPost(string $title, string $content): int
    {
        if (empty(trim($title))) {
            throw new \InvalidArgumentException('Title cannot be empty');
        }
        if (empty(trim($content))) {
            throw new \InvalidArgumentException('Content cannot be empty');
        }
        
        return $this->repository->insert(trim($title), trim($content));
    }

    public function updatePost(int $id, string $title, string $content): bool
    {
        if (empty(trim($title))) {
            throw new \InvalidArgumentException('Title cannot be empty');
        }
        if (empty(trim($content))) {
            throw new \InvalidArgumentException('Content cannot be empty');
        }
        
        return $this->repository->update($id, trim($title), trim($content));
    }

    public function deletePost(int $id): bool
    {
        return $this->repository->softDelete($id);
    }
}

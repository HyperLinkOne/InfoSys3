<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BlogPost;

class BlogPostRepository extends BaseRepository
{
    protected string $table = 'blog_posts';

    /** @return BlogPost[] */
    public function findAll(): array
    {
        $query = "SELECT * FROM $this->table WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $rows = $this->getAll($query);
        return array_map(fn($row) => $this->mapRowToEntity($row), $rows);
    }

    public function find(int $id): ?BlogPost
    {
        $query = "SELECT * FROM $this->table  WHERE deleted_at IS NULL AND id = :id";
        $row = $this->getOne($query, ['id' => $id]);
        if (empty($row)) {
            return null;
        }

        /** @var BlogPost $blogPost */
        $blogPost = $this->denormalizer->denormalize($row, BlogPost::class);
        return $blogPost;
    }

    public function insert(string $title, string $content): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO $this->table (title, content, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$title, $content]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare("UPDATE $this->table SET title = ?, content = ? WHERE id = ?");
        return $stmt->execute([$title, $content, $id]);
    }

    public function hardDelete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE $this->table SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function mapRowToEntity(array $row): BlogPost
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->denormalizer->denormalize($row, BlogPost::class);
        return $blogPost;
    }
}

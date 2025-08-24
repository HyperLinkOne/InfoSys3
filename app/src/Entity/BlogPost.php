<?php

declare(strict_types=1);

namespace App\Entity;

class BlogPost
{
    private ?int $id;
    private string $title;
    private string $content;
    private \DateTimeImmutable $createdAt;

    private ?\DateTimeImmutable $deletedAt;

    public function __construct(?int $id, string $title, string $content, \DateTimeImmutable $createdAt, ?\DateTimeImmutable $deletedAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->deletedAt = $deletedAt;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}

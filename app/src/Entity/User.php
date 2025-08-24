<?php

declare(strict_types=1);

namespace App\Entity;

class User
{
    private ?int $id;
    private string $username;
    private string $email;
    private string $passwordHash;
    private string $role;
    private ?string $firstName;
    private ?string $lastName;
    private bool $isActive;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $lastLoginAt;
    private ?\DateTimeImmutable $deletedAt;

    public function __construct(
        ?int $id,
        string $username,
        string $email,
        string $passwordHash,
        string $role,
        ?string $firstName,
        ?string $lastName,
        bool $isActive,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        ?\DateTimeImmutable $lastLoginAt,
        ?\DateTimeImmutable $deletedAt
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->lastLoginAt = $lastLoginAt;
        $this->deletedAt = $deletedAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName) ?: $this->username;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    // Setters for updating user data
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): void
    {
        $this->lastLoginAt = $lastLoginAt;
    }
}

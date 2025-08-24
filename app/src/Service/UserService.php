<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllUsers(): array
    {
        return $this->repository->findAll();
    }

    public function getUser(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->repository->findByUsername($username);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    public function getUsersByRole(string $role): array
    {
        return $this->repository->findByRole($role);
    }

    public function createUser(
        string $username,
        string $email,
        string $password,
        string $role = 'user',
        ?string $firstName = null,
        ?string $lastName = null
    ): int {
        // Validation
        if (empty(trim($username))) {
            throw new \InvalidArgumentException('Username cannot be empty');
        }
        if (empty(trim($email))) {
            throw new \InvalidArgumentException('Email cannot be empty');
        }
        if (empty(trim($password))) {
            throw new \InvalidArgumentException('Password cannot be empty');
        }
        if (!in_array($role, ['admin', 'user'], true)) {
            throw new \InvalidArgumentException('Invalid role');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        // Check if username already exists
        if ($this->repository->findByUsername($username)) {
            throw new \InvalidArgumentException('Username already exists');
        }

        // Check if email already exists
        if ($this->repository->findByEmail($email)) {
            throw new \InvalidArgumentException('Email already exists');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        return $this->repository->insert($username, $email, $passwordHash, $role, $firstName, $lastName);
    }

    public function updateUser(
        int $id,
        string $username,
        string $email,
        ?string $firstName,
        ?string $lastName,
        string $role,
        bool $isActive
    ): bool {
        // Validation
        if (empty(trim($username))) {
            throw new \InvalidArgumentException('Username cannot be empty');
        }
        if (empty(trim($email))) {
            throw new \InvalidArgumentException('Email cannot be empty');
        }
        if (!in_array($role, ['admin', 'user'], true)) {
            throw new \InvalidArgumentException('Invalid role');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        // Check if user exists
        $existingUser = $this->repository->find($id);
        if (!$existingUser) {
            throw new \InvalidArgumentException('User not found');
        }

        // Check if username already exists (excluding current user)
        $userWithUsername = $this->repository->findByUsername($username);
        if ($userWithUsername && $userWithUsername->getId() !== $id) {
            throw new \InvalidArgumentException('Username already exists');
        }

        // Check if email already exists (excluding current user)
        $userWithEmail = $this->repository->findByEmail($email);
        if ($userWithEmail && $userWithEmail->getId() !== $id) {
            throw new \InvalidArgumentException('Email already exists');
        }

        return $this->repository->update($id, $username, $email, $firstName, $lastName, $role, $isActive);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        if (empty(trim($newPassword))) {
            throw new \InvalidArgumentException('Password cannot be empty');
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->repository->updatePassword($id, $passwordHash);
    }

    public function authenticateUser(string $username, string $password): ?User
    {
        $user = $this->repository->findByUsername($username);
        
        if (!$user || !$user->isActive()) {
            return null;
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            return null;
        }

        // Update last login
        $this->repository->updateLastLogin($user->getId());
        
        return $user;
    }

    public function deleteUser(int $id): bool
    {
        return $this->repository->softDelete($id);
    }

    public function hardDeleteUser(int $id): bool
    {
        return $this->repository->hardDelete($id);
    }

    public function validatePassword(string $password): bool
    {
        // Minimum 8 characters, at least one letter and one number
        return strlen($password) >= 8 && 
               preg_match('/[A-Za-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }
}

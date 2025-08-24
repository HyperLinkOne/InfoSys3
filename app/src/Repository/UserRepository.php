<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;

class UserRepository extends BaseRepository
{
    protected string $table = 'users';

    /** @return User[] */
    public function findAll(): array
    {
        $query = "SELECT * FROM $this->table WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $rows = $this->getAll($query);
        return array_map(fn($row) => $this->mapRowToEntity($row), $rows);
    }

    public function find(int $id): ?User
    {
        $query = "SELECT * FROM $this->table WHERE deleted_at IS NULL AND id = :id";
        $row = $this->getOne($query, ['id' => $id]);
        if (empty($row)) {
            return null;
        }

        return $this->mapRowToEntity($row);
    }

    public function findByUsername(string $username): ?User
    {
        $query = "SELECT * FROM $this->table WHERE deleted_at IS NULL AND username = :username";
        $row = $this->getOne($query, ['username' => $username]);
        if (empty($row)) {
            return null;
        }

        return $this->mapRowToEntity($row);
    }

    public function findByEmail(string $email): ?User
    {
        $query = "SELECT * FROM $this->table WHERE deleted_at IS NULL AND email = :email";
        $row = $this->getOne($query, ['email' => $email]);
        if (empty($row)) {
            return null;
        }

        return $this->mapRowToEntity($row);
    }

    public function findByRole(string $role): array
    {
        $query = "SELECT * FROM $this->table WHERE deleted_at IS NULL AND role = :role ORDER BY created_at DESC";
        $rows = $this->getAll($query, ['role' => $role]);
        return array_map(fn($row) => $this->mapRowToEntity($row), $rows);
    }

    public function insert(string $username, string $email, string $passwordHash, string $role, ?string $firstName, ?string $lastName): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO $this->table (username, email, password_hash, role, first_name, last_name, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$username, $email, $passwordHash, $role, $firstName, $lastName]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $username, string $email, ?string $firstName, ?string $lastName, string $role, bool $isActive): bool
    {
        $active = $isActive ? 1 : 0;
        $stmt = $this->pdo->prepare("
            UPDATE $this->table 
            SET username = ?, email = ?, first_name = ?, last_name = ?, role = ?, is_active = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$username, $email, $firstName, $lastName, $role, $active, $id]);
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        $stmt = $this->pdo->prepare("UPDATE $this->table SET password_hash = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$passwordHash, $id]);
    }

    public function updateLastLogin(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE $this->table SET last_login_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE $this->table SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function hardDelete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$id]);
    }

    private function mapRowToEntity(array $row): User
    {
        /** @var User $user */

        return $this->denormalizer->denormalize($row, User::class);
    }

    private function mapRowToEntity_old(array $row): User
    {
        return new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $row['password_hash'],
            $row['role'],
            $row['first_name'],
            $row['last_name'],
            (bool)$row['is_active'],
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at']),
            $row['last_login_at'] ? new \DateTimeImmutable($row['last_login_at']) : null,
            $row['deleted_at'] ? new \DateTimeImmutable($row['deleted_at']) : null
        );
    }
}

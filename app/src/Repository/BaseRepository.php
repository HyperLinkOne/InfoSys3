<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Serializer\Denormalizer\Denormalizer;
use App\Service\DatabaseService;
use PDO;


abstract class BaseRepository
{
    protected PDO $pdo;
    protected string $table;

    protected Denormalizer $denormalizer;

    public function __construct(DatabaseService $db)
    {
        $this->pdo = $db->getConnection();
        $this->denormalizer = new Denormalizer();
    }

    public function getAll(string $sql, ?array $parameters = null): array
    {
        $stmt = $this->pdo->prepare($sql);
        if(is_null($parameters)) {
            $stmt->execute();
        } else {
            $stmt->execute($parameters);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne(string $sql, array $parameters): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}

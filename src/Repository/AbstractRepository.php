<?php

declare(strict_types=1);

namespace App\Repository;

abstract class AbstractRepository
{
    protected ?\PDO $pdo = null;

    public function __construct()
    {
        $this->createConnection();
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->prepareAndExecute($sql, $params)->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->prepareAndExecute($sql, $params)->fetch();
        return is_array($result) ? $result : null;
    }

    protected function execute(string $sql, array $params = []): void
    {
        $this->prepareAndExecute($sql, $params);
    }

    private function prepareAndExecute(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    private function createConnection(): void
    {
        try {
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;',
                filter_var($_ENV['DB_HOST'], FILTER_VALIDATE_IP, FILTER_THROW_ON_FAILURE),
                filter_var($_ENV['DB_PORT'], FILTER_VALIDATE_INT, FILTER_THROW_ON_FAILURE),
                $_ENV['DB_NAME']
            );

            $pdo = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(\PDO::ATTR_TIMEOUT, 5);

            $this->pdo = $pdo;
        } catch(\PDOException) {
            throw new \RuntimeException('Database connection failed');
        }
    }
}
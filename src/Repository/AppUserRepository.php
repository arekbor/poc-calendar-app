<?php

declare(strict_types=1);

namespace App\Repository;

final class AppUserRepository extends AbstractRepository
{
    public function create(array $user): int
    {
        $sql = '
            INSERT INTO app_user (username, email, password)
            VALUES
            (:username, :email, :password)
        ';

        $this->execute($sql, $user);
        $lastInsertId = $this->pdo->lastInsertId('app_user_id_seq');
        return intval($lastInsertId);
    }

    public function getByEmail(string $email): ?array
    {
        $sql = 'SELECT id, username, email, password FROM app_user WHERE email = (:email)';
        return $this->fetchOne($sql, ['email' => $email]);
    }

    public function isUserWithEmailExists(string $email): bool
    {
        $sql = 'SELECT COUNT(id) FROM app_user WHERE email = (:email)';
        $result = $this->fetchOne($sql, ['email' => $email]);

        return intval($result['count']) > 0;
    }
}
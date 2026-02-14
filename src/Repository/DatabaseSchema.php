<?php

declare(strict_types=1);

namespace App\Repository;

final class DatabaseSchema extends AbstractRepository
{
    public function createTables(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS calendar_events (
                id SERIAL PRIMARY KEY,
                title VARCHAR(50) NOT NULL,
                start_date TIMESTAMPTZ NOT NULL,
                end_date TIMESTAMPTZ NOT NULL,
                color VARCHAR(50) NULL
            );
        ";

        $this->execute($sql);

        $sql = "
            CREATE TABLE IF NOT EXISTS app_user (
                id SERIAL PRIMARY KEY,
                username VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(200) NOT NULL
            );
        ";

        $this->execute($sql);
    }
}
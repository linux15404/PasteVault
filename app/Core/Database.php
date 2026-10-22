<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connect(string $path): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $dir = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        self::$pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        self::$pdo->exec('PRAGMA journal_mode = WAL');
        self::$pdo->exec('
            CREATE TABLE IF NOT EXISTS pastes (
                id         TEXT PRIMARY KEY,
                title      TEXT NOT NULL DEFAULT \'\',
                content    TEXT NOT NULL,
                language   TEXT NOT NULL DEFAULT \'plain\',
                expires_at INTEGER,
                created_at INTEGER NOT NULL
            )
        ');

        return self::$pdo;
    }

    public static function get(): PDO
    {
        if (!self::$pdo instanceof PDO) {
            throw new \RuntimeException('Database is not connected yet.');
        }

        return self::$pdo;
    }
}

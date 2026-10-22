<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Paste;
use PDO;

final class PasteService
{
    private const EXPIRY_OPTIONS = [
        'never' => null,
        'hour' => 3600,
        'day' => 86400,
        'week' => 604800,
        'month' => 2592000,
    ];

    public function create(string $content, string $title, string $language, string $expiry): Paste
    {
        $ttl = self::EXPIRY_OPTIONS[$expiry] ?? null;
        $title = trim($title);
        // Без mbstring обрезаем по байтам — заголовки и так короткие, ломать UTF-8 не страшно при 120.
        $title = function_exists('mb_substr') ? mb_substr($title, 0, 120) : substr($title, 0, 120);

        $paste = new Paste(
            id: bin2hex(random_bytes(5)),
            title: $title,
            content: $content,
            language: preg_match('/^[a-z0-9+#.-]+$/', $language) ? $language : 'plain',
            expiresAt: $ttl !== null ? time() + $ttl : null,
            createdAt: time(),
        );

        $stmt = Database::get()->prepare(
            'INSERT INTO pastes (id, title, content, language, expires_at, created_at)
             VALUES (:id, :title, :content, :language, :expires_at, :created_at)'
        );

        $stmt->execute([
            'id' => $paste->id,
            'title' => $paste->title,
            'content' => $paste->content,
            'language' => $paste->language,
            'expires_at' => $paste->expiresAt,
            'created_at' => $paste->createdAt,
        ]);

        return $paste;
    }

    public function find(string $id): ?Paste
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $id)) {
            return null;
        }

        $stmt = Database::get()->prepare('SELECT * FROM pastes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        $paste = Paste::fromRow($row);

        if ($paste->isExpired()) {
            $this->delete($id);

            return null;
        }

        return $paste;
    }

    public function delete(string $id): void
    {
        $stmt = Database::get()->prepare('DELETE FROM pastes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /** Удаляет истёкшие пасты. Вызывается из bin/cleanup.php по cron. */
    public function purgeExpired(): int
    {
        $stmt = Database::get()->prepare('DELETE FROM pastes WHERE expires_at IS NOT NULL AND expires_at <= :now');
        $stmt->execute(['now' => time()]);

        return $stmt->rowCount();
    }

    /** @return array<int, Paste> */
    public function recent(int $limit = 10): array
    {
        $stmt = Database::get()->prepare(
            'SELECT * FROM pastes
             WHERE expires_at IS NULL OR expires_at > :now
             ORDER BY created_at DESC LIMIT :limit'
        );

        $stmt->bindValue('now', time(), PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(Paste::fromRow(...), $stmt->fetchAll());
    }
}

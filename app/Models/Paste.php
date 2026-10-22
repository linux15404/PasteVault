<?php

declare(strict_types=1);

namespace App\Models;

final class Paste
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $content,
        public readonly string $language,
        public readonly ?int $expiresAt,
        public readonly int $createdAt,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id: (string) $row['id'],
            title: (string) $row['title'],
            content: (string) $row['content'],
            language: (string) $row['language'],
            expiresAt: $row['expires_at'] !== null ? (int) $row['expires_at'] : null,
            createdAt: (int) $row['created_at'],
        );
    }

    public function isExpired(): bool
    {
        return $this->expiresAt !== null && $this->expiresAt <= time();
    }
}

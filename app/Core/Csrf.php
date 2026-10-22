<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::token() . '">';
    }

    public static function verify(): void
    {
        $sent = $_POST['csrf_token'] ?? '';

        if (!hash_equals(self::token(), (string) $sent)) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }
    }
}

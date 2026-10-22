<?php

declare(strict_types=1);

/**
 * Точка входа для встроенного PHP-сервера:
 *   php -S localhost:8000 router.php
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path !== '/' && is_file(__DIR__ . '/public' . $path)) {
    return false; // отдаём статику как есть
}

require __DIR__ . '/public/index.php';

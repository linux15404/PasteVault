<?php

declare(strict_types=1);

/**
 * CLI: удаляет истёкшие пасты.
 * Запуск по cron: 0 * * * * php /path/to/bin/cleanup.php хуй))
 */

require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Models/Paste.php';
require __DIR__ . '/../app/Services/PasteService.php';

use App\Core\Database;
use App\Services\PasteService;

if (PHP_SAPI !== 'cli') {
    exit("This script can only be run from the command line.\n");
}

Database::connect(__DIR__ . '/../database/pastevault.sqlite');

$deleted = (new PasteService())->purgeExpired();

echo sprintf("Cleaned up %d expired paste(s).\n", $deleted);

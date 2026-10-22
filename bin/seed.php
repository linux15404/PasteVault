<?php

declare(strict_types=1);

/**
 * Наполняет базу демонстрационными пастами.
 * Запуск: php bin/seed.php
 */

require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Models/Paste.php';
require __DIR__ . '/../app/Services/PasteService.php';

use App\Core\Database;
use App\Services\PasteService;

if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

Database::connect(__DIR__ . '/../database/pastevault.sqlite');
$service = new PasteService();

$demos = [
    [
        'title' => 'PHP: fast router example',
        'language' => 'php',
        'content' => <<<'PHP'
<?php

declare(strict_types=1);

// Микро-роутер: сопоставляет путь с обработчиком
$routes = [
    'GET /'       => fn() => 'Home',
    'GET /about'  => fn() => 'About us',
    'POST /login' => fn() => 'Login handler',
];

$method = $_SERVER['REQUEST_METHOD'];
$path   = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') ?: '/';
$key    = "$method $path";

if (isset($routes[$key])) {
    echo $routes[$key]();
} {
    http_response_code(404);
    echo 'Not found';
}
PHP,
    ],
    [
        'title' => 'Python: asyncio fetch example',
        'language' => 'python',
        'content' => <<<'PY'
import asyncio
import aiohttp

URLS = [
    "https://api.github.com",
    "https://httpbin.org/get",
]

async def fetch(session, url):
    async with session.get(url, timeout=aiohttp.ClientTimeout(total=10)) as resp:
        body = await resp.text()
        print(f"{url} -> {resp.status}, {len(body)} bytes")

async def main():
    async with aiohttp.ClientSession() as session:
        await asyncio.gather(*(fetch(session, u) for u in URLS))

if __name__ == "__main__":
    asyncio.run(main())
PY,
    ],
    [
        'title' => 'FileFlow: find large files',
        'language' => 'bash',
        'content' => <<<'BASH'
# Найти 10 самых больших файлов в домашней папке
find ~ -type f -not -path '*/.*' -printf '%s %p\n' 2>/dev/null \
  | sort -rn \
  | head -10 \
  | awk '{printf "%.1f MB  %s\n", $1/1024/1024, $2}'

# FileFlow делает то же самое одной командой:
#   fileflow report ~ --top 10
BASH,
    ],
    [
        'title' => 'SQLite: useful queries',
        'language' => 'sql',
        'content' => <<<'SQL'
-- Топ-5 самых больших паст
SELECT id, title, LENGTH(content) AS size
FROM pastes
ORDER BY size DESC
LIMIT 5;

-- Статистика по языкам
SELECT language, COUNT(*) AS pastes
FROM pastes
GROUP BY language
ORDER BY pastes DESC;
SQL,
    ],
];

foreach ($demos as $demo) {
    $paste = $service->create($demo['content'], $demo['title'], $demo['language'], 'never');
    echo "Created: /paste/{$paste->id}  ({$demo['title']})\n";
}

echo "Done.\n";

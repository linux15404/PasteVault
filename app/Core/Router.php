<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: array{0: class-string, 1: string}}>> */
    private array $routes = [];

    public function get(string $pattern, array $handler): void
    {
        $this->routes[] = ['method' => 'GET', 'pattern' => $pattern, 'handler' => $handler];
    }

    public function post(string $pattern, array $handler): void
    {
        $this->routes[] = ['method' => 'POST', 'pattern' => $pattern, 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $regex = '#^' . preg_replace('#\{([a-z]+)\}#', '(?P<$1>[a-zA-Z0-9]+)', $route['pattern']) . '$#';

            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $route['handler'];

                (new $class())->$action(...array_values($params));

                return;
            }
        }

        http_response_code(404);
        echo $this->view('404', ['title' => '404 — Not Found']);
    }

    public function view(string $name, array $data = []): string
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../Views/' . $name . '.php';

        return (string) ob_get_clean();
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Router;
use App\Services\PasteService;

final class HomeController
{
    public function index(): void
    {
        $service = new PasteService();

        $this->render('home', [
            'title' => 'PasteVault — share code in seconds',
            'recent' => $service->recent(),
            'flash' => $_SESSION['flash'] ?? null,
        ]);

        unset($_SESSION['flash']);
    }

    private function render(string $view, array $data): void
    {
        $router = new Router();
        echo $router->view($view, $data);
    }
}

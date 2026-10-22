<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Router;
use App\Services\PasteService;

final class PasteController
{
    public function store(): void
    {
        Csrf::verify();

        $content = (string) ($_POST['content'] ?? '');
        $title = (string) ($_POST['title'] ?? '');
        $language = (string) ($_POST['language'] ?? 'plain');
        $expiry = (string) ($_POST['expiry'] ?? 'never');

        if (trim($content) === '' || strlen($content) > 512_000) {
            $_SESSION['flash'] = 'Paste must be between 1 byte and 500 KB.';

            header('Location: /');

            return;
        }

        $paste = (new PasteService())->create($content, $title, $language, $expiry);

        header('Location: /paste/' . $paste->id, true, 303);
    }

    public function show(string $id): void
    {
        $paste = (new PasteService())->find($id);
        $router = new Router();

        if ($paste === null) {
            http_response_code(404);
            echo $router->view('404', ['title' => '404 — Paste not found']);

            return;
        }

        echo $router->view('paste', [
            'title' => ($paste->title !== '' ? $paste->title : 'Untitled') . ' — PasteVault',
            'paste' => $paste,
            'lineCount' => substr_count($paste->content, "\n") + 1,
        ]);
    }

    public function raw(string $id): void
    {
        $paste = (new PasteService())->find($id);

        if ($paste === null) {
            http_response_code(404);
            header('Content-Type: text/plain');
            exit('Paste not found');
        }

        header('Content-Type: text/plain; charset=utf-8');
        exit($paste->content);
    }

    public function destroy(string $id): void
    {
        Csrf::verify();
        (new PasteService())->delete($id);

        header('Location: /', true, 303);
    }
}

<?php

declare(strict_types=1);

require __DIR__ . '/../app/Core/Router.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Core/Csrf.php';
require __DIR__ . '/../app/Models/Paste.php';
require __DIR__ . '/../app/Services/PasteService.php';
require __DIR__ . '/../app/Controllers/HomeController.php';
require __DIR__ . '/../app/Controllers/PasteController.php';

use App\Controllers\HomeController;
use App\Controllers\PasteController;
use App\Core\Database;
use App\Core\Router;

session_start();

$db = Database::connect(__DIR__ . '/../database/pastevault.sqlite');

$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->post('/paste', [PasteController::class, 'store']);
$router->get('/paste/{id}', [PasteController::class, 'show']);
$router->post('/paste/{id}/delete', [PasteController::class, 'destroy']);
$router->get('/raw/{id}', [PasteController::class, 'raw']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

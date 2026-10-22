<?php

$router = new App\Core\Router();

$content = ob_start();
?>
<section class="card center">
    <h1>404</h1>
    <p>This paste doesn't exist or has expired.</p>
    <p><a class="btn" href="/">Back to PasteVault</a></p>
</section>
<?php
echo $router->view('layout', ['title' => $title, 'content' => ob_get_clean()]);

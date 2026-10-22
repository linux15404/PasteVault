<?php

/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/assets/style.css">
    <script src="/assets/highlight.js" defer></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="/">🔐 PasteVault</a>
    <nav>
        <a href="/">New paste</a>
    </nav>
</header>

<main class="container">
    <?= $content ?>
</main>

<footer class="site-footer">
    <p>PasteVault · PHP &amp; SQLite</p>
</footer>
</body>
</html>

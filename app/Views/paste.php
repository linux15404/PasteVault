<?php

use App\Core\Csrf;

/** @var \App\Models\Paste $paste */
/** @var int $lineCount */
$router = new App\Core\Router();

$lines = explode("\n", $paste->content);
$gutter = implode("\n", array_map(strval(...), range(1, $lineCount)));

$content = ob_start();
?>
<article class="card">
    <header class="paste-header">
        <h1><?= htmlspecialchars($paste->title !== '' ? $paste->title : 'Untitled') ?></h1>
        <div class="meta">
            <span><?= htmlspecialchars($paste->language) ?></span>
            <span><?= $lineCount ?> lines</span>
            <span><?= date('M j, Y H:i', $paste->createdAt) ?></span>
            <?php if ($paste->expiresAt !== null): ?>
                <span>expires <?= date('M j, Y', $paste->expiresAt) ?></span>
            <?php endif; ?>
        </div>
    </header>

    <div class="code-view">
        <pre class="gutter" aria-hidden="true"><?= htmlspecialchars($gutter) ?></pre>
        <pre class="code"><code data-lang="<?= htmlspecialchars($paste->language) ?>"><?= htmlspecialchars($paste->content) ?></code></pre>
    </div>

    <div class="actions">
        <a class="btn" href="/raw/<?= htmlspecialchars($paste->id) ?>" target="_blank" rel="noopener">Raw</a>
        <button class="btn" type="button"
                onclick="navigator.clipboard.writeText(location.href).then(() => this.textContent='Copied!')">
            Copy link
        </button>

        <form method="post" action="/paste/<?= htmlspecialchars($paste->id) ?>/delete"
              onsubmit="return confirm('Delete this paste?')">
            <?= Csrf::field() ?>
            <button class="btn btn-danger" type="submit">Delete</button>
        </form>
    </div>
</article>
<?php
echo $router->view('layout', ['title' => $title, 'content' => ob_get_clean()]);

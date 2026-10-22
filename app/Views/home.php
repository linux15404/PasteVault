<?php

use App\Core\Csrf;

/** @var array{flash: ?string, recent: array} $data — из HomeController */
$router = new App\Core\Router();

$content = ob_start();
?>
<?php if (!empty($flash)): ?>
    <p class="flash"><?= htmlspecialchars($flash) ?></p>
<?php endif; ?>

<section class="card">
    <h1>New paste</h1>

    <form method="post" action="/paste">
        <?= Csrf::field() ?>

        <input class="input" type="text" name="title" placeholder="Title (optional)" maxlength="120">

        <textarea class="input code" name="content" rows="12" required
                  placeholder="Paste your code or text here…"></textarea>

        <div class="row">
            <label class="grow">
                Language
                <select class="input" name="language">
                    <option value="plain">Plain text</option>
                    <option value="php">PHP</option>
                    <option value="python">Python</option>
                    <option value="javascript">JavaScript</option>
                    <option value="json">JSON</option>
                    <option value="bash">Bash</option>
                    <option value="sql">SQL</option>
                    <option value="html">HTML</option>
                    <option value="css">CSS</option>
                </select>
            </label>

            <label class="grow">
                Expires
                <select class="input" name="expiry">
                    <option value="never">Never</option>
                    <option value="hour">1 hour</option>
                    <option value="day">1 day</option>
                    <option value="week">1 week</option>
                    <option value="month">1 month</option>
                </select>
            </label>
        </div>

        <button class="btn" type="submit">Create paste</button>
    </form>
</section>

<?php if (!empty($recent)): ?>
    <section class="card">
        <h2>Recent pastes</h2>
        <ul class="paste-list">
            <?php foreach ($recent as $item): ?>
                <li>
                    <a href="/paste/<?= htmlspecialchars($item->id) ?>">
                        <?= htmlspecialchars($item->title !== '' ? $item->title : 'Untitled') ?>
                    </a>
                    <span class="meta">
                        <?= htmlspecialchars($item->language) ?> ·
                        <?= date('M j, Y H:i', $item->createdAt) ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
<?php
echo $router->view('layout', ['title' => $title, 'content' => ob_get_clean()]);

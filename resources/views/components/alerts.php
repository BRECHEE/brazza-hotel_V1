<?php
$success = flashed('success');
$error = flashed('error');
$errors = \App\Core\Session::errors();
?>
<?php if ($success): ?>
    <div class="alert alert--success" role="status">
        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
        <p><?= e((string) $success) ?></p>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert--error" role="alert">
        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        <p><?= e((string) $error) ?></p>
    </div>
<?php endif; ?>
<?php if ($errors !== [] && !$success): ?>
    <div class="alert alert--error" role="alert">
        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        <div>
            <p>Certains champs nécessitent votre attention.</p>
            <ul class="alert__list">
                <?php foreach ($errors as $message): ?>
                    <li><?= e((string) $message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php
$label = $label ?? 'Aperçu';
$title = $title ?? 'Contenu de démonstration, à valider avec l’hôtel';
?>
<span class="demo-chip" title="<?= e($title) ?>">
    <span class="demo-chip__dot" aria-hidden="true"></span><?= e($label) ?>
</span>

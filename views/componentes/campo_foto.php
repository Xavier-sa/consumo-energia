<?php
declare(strict_types=1);

function campoFoto(string $periodo, string $rotulo): void
{
    $id = $periodo === 'morning' ? 'manhã' : 'noite';
    ?>
    <div class="photo-field">
      <input class="photo-input" type="file" id="<?= $periodo ?>-photo" name="<?= $periodo ?>_photo" accept="image/jpeg,image/png,image/webp" capture="environment">
      <label class="photo-button" for="<?= $periodo ?>-photo"><span aria-hidden="true">📷</span> <?= $rotulo ?></label>
      <div id="<?= $periodo ?>-preview" class="photo-preview hidden">
        <img alt="Prévia da foto da <?= $id ?>">
        <div><strong>Foto selecionada</strong><small class="photo-time"></small></div>
        <button class="remove-photo" type="button" data-period="<?= $periodo ?>" aria-label="Remover foto da <?= $id ?>">×</button>
      </div>
      <input type="hidden" id="<?= $periodo ?>-photo-time">
    </div>
    <?php
}

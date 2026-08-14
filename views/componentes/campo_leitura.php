<?php
declare(strict_types=1);

function campoLeitura(string $periodo, string $icone, string $titulo, string $exemplo): void
{
    $classe = $periodo === 'morning' ? 'morning-field' : 'night-field';
    $rotuloFoto = $periodo === 'morning' ? 'Adicionar foto da manhã' : 'Adicionar foto da noite';
    ?>
    <div class="reading-field <?= $classe ?>">
      <div class="reading-heading">
        <span class="reading-icon" aria-hidden="true"><?= $icone ?></span>
        <div><label for="<?= $periodo ?>"><?= $titulo ?></label><small>Valor mostrado no medidor</small></div>
      </div>
      <div class="input-with-unit">
        <input type="number" id="<?= $periodo ?>" min="0" step="1" inputmode="numeric" placeholder="Ex.: <?= $exemplo ?>" required>
        <span>kWh</span>
      </div>
      <?php campoFoto($periodo, $rotuloFoto); ?>
    </div>
    <?php
}

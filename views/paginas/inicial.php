<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/componentes/botao.php';
require_once dirname(__DIR__) . '/componentes/campo_foto.php';
require_once dirname(__DIR__) . '/componentes/campo_leitura.php';

$arquivoCss = dirname(__DIR__, 2) . '/public/assets/css/main.css';
$arquivoJs = dirname(__DIR__, 2) . '/public/assets/js/app.js';
$versaoCss = (string) (filemtime($arquivoCss) ?: time());
$versaoJs = (string) (filemtime($arquivoJs) ?: time());
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#174c3c">
  <title>Meu Consumo de Energia</title>
  <link rel="stylesheet" href="assets/css/main.css?v=<?= $versaoCss ?>">
</head>
<body>
  <main id="app">
    <section id="login-view" class="login-shell" aria-labelledby="login-title">
      <div class="brand brand-login" aria-label="Meu Consumo"><span class="brand-icon" aria-hidden="true">⚡</span><span>Meu Consumo</span></div>
      <div class="login-card">
        <p class="eyebrow">Bem-vindo de volta</p>
        <h1 id="login-title">Acompanhe sua energia</h1>
        <p class="muted">Entre para registrar as leituras do seu medidor.</p>
        <form id="login-form" novalidate>
          <label for="username">Usuário</label>
          <select id="username" autocomplete="username"><option>DARA</option><option>XAVIER</option></select>
          <label for="password">Senha</label>
          <input id="password" type="password" value="654321" autocomplete="current-password" required>
          <?php botao('Entrar', 'btn-login', 'primary', 'submit', 'button-block'); ?>
          <p id="login-error" class="message message-error" role="alert" aria-live="polite"></p>
        </form>
      </div>
    </section>

    <div id="main-view" class="hidden">
      <header class="topbar"><div class="topbar-content">
        <div class="brand"><span class="brand-icon" aria-hidden="true">⚡</span><span>Meu Consumo</span></div>
        <div class="user-area"><span class="user-greeting">Olá, <strong id="user-name"></strong></span><?php botao('Sair', 'btn-logout', 'ghost'); ?></div>
      </div></header>

      <div class="page-content">
        <section class="page-heading">
          <div><p class="eyebrow">Controle diário</p><h1>Consumo de energia</h1><p class="muted">Registre cada turno no horário adequado. Ao completar manhã e noite, o consumo do dia é calculado para você.</p></div>
          <div class="today-pill"><span aria-hidden="true">📅</span> <span id="today-label"></span></div>
        </section>

        <section class="entry-card" aria-labelledby="entry-title">
          <div class="section-title-row">
            <div><p class="eyebrow" id="form-mode">Nova leitura</p><h2 id="entry-title">Registrar medidor</h2></div>
            <?php botao('Cancelar edição', 'btn-clear', 'ghost', 'button', 'hidden'); ?>
          </div>
          <form id="entry-form" novalidate>
            <div class="date-field"><label for="date">Data da leitura</label><input type="date" id="date" required></div>
            <fieldset class="shift-selector">
              <legend>Qual turno deseja registrar?</legend>
              <label><input type="radio" name="shift" value="morning" checked><span>☀️ Manhã</span></label>
              <label><input type="radio" name="shift" value="night"><span>🌙 Noite</span></label>
            </fieldset>
            <div class="readings-grid">
              <?php campoLeitura('morning', '☀️', 'Leitura da manhã', '515'); ?>
              <?php campoLeitura('night', '🌙', 'Leitura da noite', '518'); ?>
            </div>
            <div class="consumption-preview" aria-live="polite">
              <div><span class="preview-label">Registro por turno</span><small id="preview-hint">O consumo aparecerá quando os dois turnos estiverem registrados</small></div>
            </div>
            <p id="form-message" class="message" role="status" aria-live="polite"></p>
            <?php botao('Salvar leitura', 'btn-save', 'primary', 'submit', 'button-save'); ?>
          </form>
        </section>

        <section class="history-card" aria-labelledby="history-title">
          <div class="section-title-row history-heading">
            <div><p class="eyebrow">Acompanhamento</p><h2 id="history-title">Histórico de leituras</h2></div>
            <?php botao('↓ Exportar CSV', 'btn-export', 'secondary'); ?>
          </div>
          <div id="empty-state" class="empty-state hidden"><span aria-hidden="true">📋</span><h3>Nenhuma leitura registrada</h3><p>Seu histórico aparecerá aqui depois do primeiro registro.</p></div>
          <div id="table-wrap" class="table-wrap"><table id="entries-table">
            <thead><tr><th>Data</th><th>Manhã</th><th>Noite</th><th>Consumo</th><th>Fotos</th><th><span class="sr-only">Ações</span></th></tr></thead>
            <tbody></tbody>
          </table></div>
          <nav id="pagination" class="pagination" aria-label="Páginas do histórico"></nav>
        </section>
      </div>
    </div>
  </main>
  <script type="module" src="assets/js/app.js?v=<?= $versaoJs ?>"></script>
</body>
</html>

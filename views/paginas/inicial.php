<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/componentes/botao.php';
require_once dirname(__DIR__) . '/componentes/campo_foto.php';
require_once dirname(__DIR__) . '/componentes/campo_leitura.php';
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#174c3c">
  <title>Meu Consumo de Energia</title>
  <link rel="icon" href="assets/images/icone-energia-consumo.jpeg" type="image/jpeg">
  <link rel="apple-touch-icon" href="assets/images/icone-energia-consumo.jpeg">
  <link rel="stylesheet" href="assets/css/main.css?v=20261006-1">
</head>
<body>
  <main id="app">
    <section id="login-view" class="login-shell" aria-labelledby="login-title">
      <div class="brand brand-login" aria-label="Meu Consumo"><img class="brand-icon" src="assets/images/icone-energia-consumo.jpeg" alt=""><span>Meu Consumo</span></div>
      <div class="login-card">
        <p class="eyebrow">Bem-vindo de volta</p>
        <h1 id="login-title">Acompanhe sua energia</h1>
        <p class="muted" id="auth-description">Entre para registrar as leituras do seu medidor.</p>
        <div class="auth-tabs" role="tablist" aria-label="Acesso">
          <button type="button" id="show-login" role="tab" aria-selected="true">Entrar</button>
          <button type="button" id="show-register" role="tab" aria-selected="false">Criar conta</button>
        </div>
        <form id="login-form" novalidate>
          <label for="username">Usuário</label>
          <input id="username" autocomplete="username" maxlength="30" required>
          <label for="password">Senha</label>
          <input id="password" type="password" autocomplete="current-password" required>
          <?php botao('Entrar', 'btn-login', 'primary', 'submit', 'button-block'); ?>
          <p id="login-error" class="message message-error" role="alert" aria-live="polite"></p>
        </form>
        <form id="register-form" class="hidden" novalidate>
          <label for="register-username">Escolha um usuário</label>
          <input id="register-username" autocomplete="username" minlength="3" maxlength="30" pattern="[A-Za-z0-9._-]+" required>
          <label for="register-password">Crie uma senha</label>
          <input id="register-password" type="password" autocomplete="new-password" minlength="8" maxlength="72" required>
          <label for="register-confirmation">Confirme a senha</label>
          <input id="register-confirmation" type="password" autocomplete="new-password" minlength="8" maxlength="72" required>
          <?php botao('Criar minha conta', 'btn-register', 'primary', 'submit', 'button-block'); ?>
          <p id="register-error" class="message message-error" role="alert" aria-live="polite"></p>
        </form>
      </div>
    </section>

    <div id="main-view" class="hidden">
      <header class="topbar"><div class="topbar-content">
        <div class="brand"><img class="brand-icon" src="assets/images/icone-energia-consumo.jpeg" alt=""><span>Meu Consumo</span></div>
        <div class="user-area"><span class="user-greeting">Olá, <strong id="user-name"></strong></span><?php botao('Painel admin', 'btn-admin', 'secondary', 'button', 'hidden'); ?><?php botao('Sair', 'btn-logout', 'ghost'); ?></div>
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
    <dialog id="admin-dialog" class="admin-dialog" aria-labelledby="admin-title">
      <div class="admin-modal">
        <header class="admin-header">
          <div><p class="eyebrow">Visão geral</p><h2 id="admin-title">Painel administrativo</h2><p class="muted">Consulte usuários e registros de consumo.</p></div>
          <button type="button" id="btn-close-admin" class="modal-close" aria-label="Fechar painel">×</button>
        </header>
        <p id="admin-message" class="message" role="status" aria-live="polite"></p>
        <section aria-labelledby="admin-users-title">
          <h3 id="admin-users-title">Usuários</h3>
          <div class="table-wrap admin-table-wrap"><table id="admin-users-table">
            <thead><tr><th>Usuário</th><th>Residência</th><th>Perfil</th><th>Registros</th><th>Último acesso</th></tr></thead>
            <tbody></tbody>
          </table></div>
        </section>
        <section class="admin-readings" aria-labelledby="admin-readings-title">
          <div class="admin-section-heading"><h3 id="admin-readings-title">Leituras</h3><div><label for="admin-user-filter">Filtrar por usuário</label><select id="admin-user-filter"><option value="">Todos</option></select></div></div>
          <div class="table-wrap admin-table-wrap"><table id="admin-readings-table">
            <thead><tr><th>Usuário</th><th>Data</th><th>Manhã</th><th>Noite</th><th>Consumo</th></tr></thead>
            <tbody></tbody>
          </table></div>
        </section>
      </div>
    </dialog>
  </main>
  <script type="module" src="assets/js/app.js?v=20260814-4"></script>
</body>
</html>

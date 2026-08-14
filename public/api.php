<?php
declare(strict_types=1);

use App\Aplicacao\ServicoAutenticacao;
use App\Aplicacao\ServicoLeituras;
use App\Http\Controladores\ControladorAutenticacao;
use App\Http\Controladores\ControladorLeituras;
use App\Http\Resposta;
use App\Infraestrutura\Arquivos\GerenciadorFotos;
use App\Infraestrutura\Persistencia\RepositorioLeituras;
use App\Infraestrutura\Persistencia\RepositorioUsuarios;

session_start();
$configuracao = require dirname(__DIR__) . '/bootstrap.php';

$autenticacao = new ControladorAutenticacao(
    new ServicoAutenticacao(new RepositorioUsuarios($configuracao['usuarios']))
);
$leituras = new ControladorLeituras(
    new ServicoLeituras(
        new RepositorioLeituras($configuracao['consumo']),
        new GerenciadorFotos($configuracao['uploads'], $configuracao['url_uploads'])
    )
);

$acao = $_GET['action'] ?? '';
$rotasPublicas = ['login', 'me'];
if (!in_array($acao, $rotasPublicas, true) && !isset($_SESSION['user'])) {
    Resposta::json(['message' => 'Sua sessão expirou. Entre novamente.'], 401);
    exit;
}

try {
    switch ($acao) {
        case 'login': $autenticacao->entrar(); break;
        case 'logout': $autenticacao->sair(); break;
        case 'me': $autenticacao->usuarioAtual(); break;
        case 'entries': $leituras->listar(); break;
        case 'create_entry': $leituras->criar(); break;
        case 'update_entry': $leituras->atualizar(); break;
        case 'delete_entry': $leituras->excluir(); break;
        case 'export': $leituras->exportar(); break;
        default: Resposta::json(['message' => 'Ação desconhecida.'], 404);
    }
} catch (DomainException $erro) {
    Resposta::json(['message' => $erro->getMessage()], 422);
} catch (RuntimeException $erro) {
    error_log('[consumo.infraestrutura] ' . $erro->getMessage());
    Resposta::json(['message' => $erro->getMessage()], 500);
} catch (Throwable $erro) {
    error_log('[consumo.inesperado] ' . $erro->getMessage());
    Resposta::json(['message' => 'Ocorreu um erro inesperado.'], 500);
}

<?php
declare(strict_types=1);

namespace App\Http\Controladores;

use App\Aplicacao\ServicoAutenticacao;
use App\Http\Requisicao;
use App\Http\Resposta;

final class ControladorAutenticacao
{
    private ServicoAutenticacao $servico;

    public function __construct(ServicoAutenticacao $servico)
    {
        $this->servico = $servico;
    }

    public function entrar(): void
    {
        $dados = Requisicao::corpo();
        $nome = trim((string) ($dados['username'] ?? ''));
        $senha = (string) ($dados['password'] ?? '');
        if (!$nome || !$senha) {
            Resposta::json(['message' => 'Informe usuário e senha.'], 422);
            return;
        }
        $usuario = $this->servico->entrar($nome, $senha, Requisicao::ip());
        if (!$usuario) {
            Resposta::json(['message' => 'Usuário ou senha inválidos.'], 401);
            return;
        }
        session_regenerate_id(true);
        $_SESSION['user'] = $usuario;
        Resposta::json(['ok' => true, 'user' => $usuario]);
    }

    public function cadastrar(): void
    {
        $dados = Requisicao::corpo();
        $senha = (string) ($dados['password'] ?? '');
        if ($senha !== (string) ($dados['password_confirmation'] ?? '')) {
            Resposta::json(['message' => 'A confirmação da senha não confere.'], 422);
            return;
        }
        $usuario = $this->servico->cadastrar(
            (string) ($dados['username'] ?? ''),
            $senha,
            Requisicao::ip(),
            ($dados['aceite_documentos'] ?? null) === true
        );
        session_regenerate_id(true);
        $_SESSION['user'] = $usuario;
        Resposta::json(['ok' => true, 'user' => $usuario], 201);
    }

    public function sair(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
        }
        session_destroy();
        Resposta::json(['ok' => true]);
    }

    public function usuarioAtual(): void
    {
        Resposta::json(['user' => $_SESSION['user'] ?? null]);
    }
}

<?php
declare(strict_types=1);

namespace App\Aplicacao;

use App\Dominio\Usuario;
use App\Infraestrutura\Persistencia\RepositorioUsuarios;

final class ServicoAutenticacao
{
    private RepositorioUsuarios $usuarios;

    public function __construct(RepositorioUsuarios $usuarios)
    {
        $this->usuarios = $usuarios;
    }

    public function entrar(string $nome, string $senha, string $ip): ?array
    {
        return $this->usuarios->autenticar($nome, $senha, $ip);
    }

    public function cadastrar(string $nome, string $senha, string $ip): array
    {
        $nome = strtoupper(trim($nome));
        Usuario::validar($nome, $senha);
        return $this->usuarios->cadastrar($nome, $senha, $ip);
    }
}

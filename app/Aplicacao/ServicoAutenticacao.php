<?php
declare(strict_types=1);

namespace App\Aplicacao;

use App\Dominio\Usuario;
use App\Dominio\DocumentosLegais;
use App\Infraestrutura\Persistencia\RepositorioUsuarios;
use DomainException;

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

    public function cadastrar(string $nome, string $senha, string $ip, bool $aceiteDocumentos): array
    {
        if (!$aceiteDocumentos) {
            throw new DomainException('Para criar sua conta, leia e aceite os Termos de Uso e a Política de Privacidade.');
        }
        $nome = strtoupper(trim($nome));
        Usuario::validar($nome, $senha);
        return $this->usuarios->cadastrar(
            $nome,
            $senha,
            $ip,
            gmdate('c'),
            DocumentosLegais::VERSAO_TERMOS,
            DocumentosLegais::VERSAO_PRIVACIDADE
        );
    }
}

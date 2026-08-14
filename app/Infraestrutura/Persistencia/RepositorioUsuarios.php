<?php
declare(strict_types=1);

namespace App\Infraestrutura\Persistencia;

final class RepositorioUsuarios extends RepositorioJson
{
    public function autenticar(string $nome, string $senha, string $ip): ?array
    {
        $usuarios = $this->ler();
        foreach ($usuarios as &$usuario) {
            if (($usuario['username'] ?? '') !== $nome || ($usuario['password'] ?? '') !== $senha) continue;

            $usuario['last_ip'] = $ip;
            $usuario['accesses'] = $usuario['accesses'] ?? [];
            $usuario['accesses'][] = ['at' => date('c'), 'ip' => $ip];
            $this->gravar($usuarios);
            return ['username' => $nome];
        }
        return null;
    }
}

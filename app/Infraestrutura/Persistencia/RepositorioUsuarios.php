<?php
declare(strict_types=1);

namespace App\Infraestrutura\Persistencia;

use DomainException;

final class RepositorioUsuarios extends RepositorioJson
{
    public function autenticar(string $nome, string $senha, string $ip): ?array
    {
        $usuarios = $this->ler();
        foreach ($usuarios as $indice => &$usuario) {
            if (strcasecmp((string) ($usuario['username'] ?? ''), $nome) !== 0) continue;
            $valida = isset($usuario['password_hash'])
                ? password_verify($senha, (string) $usuario['password_hash'])
                : isset($usuario['password']) && hash_equals((string) $usuario['password'], $senha);
            if (!$valida) continue;

            $usuario['identificador'] = (int) ($usuario['identificador'] ?? $this->proximoIdentificador($usuarios));
            $usuario['residencia_id'] = (int) ($usuario['residencia_id'] ?? 1);
            $usuario['role'] = (string) ($usuario['role'] ?? (strcasecmp((string) $usuario['username'], 'XAVIER') === 0 ? 'admin' : 'user'));
            if (!isset($usuario['password_hash'])) {
                $usuario['password_hash'] = password_hash($senha, PASSWORD_DEFAULT);
                unset($usuario['password']);
            }
            $usuario['last_ip'] = $ip;
            $usuario['accesses'] = $usuario['accesses'] ?? [];
            $usuario['accesses'][] = ['at' => date('c'), 'ip' => $ip];
            $this->gravar($usuarios);
            return $this->usuarioDaSessao($usuario);
        }
        return null;
    }

    public function cadastrar(string $nome, string $senha, string $ip): array
    {
        $usuarios = $this->ler();
        foreach ($usuarios as $usuario) {
            if (strcasecmp((string) ($usuario['username'] ?? ''), $nome) === 0) {
                throw new DomainException('Não foi possível criar a conta com esses dados.');
            }
        }

        $residencias = array_map(fn(array $usuario): int => (int) ($usuario['residencia_id'] ?? 1), $usuarios);
        $usuario = [
            'identificador' => $this->proximoIdentificador($usuarios),
            'username' => $nome,
            'password_hash' => password_hash($senha, PASSWORD_DEFAULT),
            'residencia_id' => $residencias ? max($residencias) + 1 : 1,
            'role' => 'user',
            'last_ip' => $ip,
            'accesses' => [['at' => date('c'), 'ip' => $ip]],
        ];
        $usuarios[] = $usuario;
        $this->gravar($usuarios);
        return $this->usuarioDaSessao($usuario);
    }

    private function proximoIdentificador(array $usuarios): int
    {
        $ids = array_map(fn(array $usuario): int => (int) ($usuario['identificador'] ?? 0), $usuarios);
        return $ids ? max($ids) + 1 : 1;
    }

    private function usuarioDaSessao(array $usuario): array
    {
        return [
            'identificador' => (int) $usuario['identificador'],
            'username' => (string) $usuario['username'],
            'residencia_id' => (int) $usuario['residencia_id'],
            'role' => (string) ($usuario['role'] ?? 'user'),
        ];
    }

    public function listarAdministracao(): array
    {
        return array_map(function (array $usuario, int $indice): array {
            $acessos = $usuario['accesses'] ?? [];
            $ultimo = $acessos ? end($acessos) : null;
            return [
                'identificador' => (int) ($usuario['identificador'] ?? $indice + 1),
                'username' => (string) ($usuario['username'] ?? ''),
                'residencia_id' => (int) ($usuario['residencia_id'] ?? 1),
                'role' => (string) ($usuario['role'] ?? (strcasecmp((string) ($usuario['username'] ?? ''), 'XAVIER') === 0 ? 'admin' : 'user')),
                'ultimo_acesso' => is_array($ultimo) ? ($ultimo['at'] ?? null) : null,
            ];
        }, $this->ler(), array_keys($this->ler()));
    }
}

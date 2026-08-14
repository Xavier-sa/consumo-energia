<?php
declare(strict_types=1);

namespace App\Aplicacao;

use App\Infraestrutura\Persistencia\RepositorioLeituras;
use App\Infraestrutura\Persistencia\RepositorioUsuarios;

final class ServicoAdministracao
{
    private RepositorioUsuarios $usuarios;
    private RepositorioLeituras $leituras;

    public function __construct(RepositorioUsuarios $usuarios, RepositorioLeituras $leituras)
    {
        $this->usuarios = $usuarios;
        $this->leituras = $leituras;
    }

    public function painel(): array
    {
        $usuarios = $this->usuarios->listarAdministracao();
        $leiturasOriginais = $this->leituras->todasAdministracao();
        $moradores = [];
        foreach ($usuarios as $usuario) {
            $moradores[$usuario['residencia_id']][] = $usuario['username'];
        }
        foreach ($usuarios as &$usuario) {
            $usuario['total_leituras'] = count(array_filter($leiturasOriginais, fn(array $leitura): bool => (int) ($leitura['residencia_id'] ?? 1) === $usuario['residencia_id']));
        }
        unset($usuario);

        $leituras = array_map(function (array $leitura) use ($moradores): array {
            $residenciaId = (int) ($leitura['residencia_id'] ?? 1);
            return [
                'identificador' => (int) ($leitura['identificador'] ?? 0),
                'residencia_id' => $residenciaId,
                'usuarios' => $moradores[$residenciaId] ?? [],
                'data' => (string) ($leitura['data'] ?? ''),
                'leitura_manha' => $leitura['leitura_manha'] ?? null,
                'leitura_noite' => $leitura['leitura_noite'] ?? null,
                'consumo' => $leitura['consumo'] ?? null,
            ];
        }, $leiturasOriginais);
        usort($leituras, fn(array $a, array $b): int => strcmp($b['data'], $a['data']));
        return ['usuarios' => $usuarios, 'leituras' => $leituras];
    }
}

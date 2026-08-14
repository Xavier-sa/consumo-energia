<?php
declare(strict_types=1);

namespace App\Infraestrutura\Persistencia;

final class RepositorioLeituras extends RepositorioJson
{
    public function paginar(int $pagina, int $limite, int $residenciaId): array
    {
        $leituras = array_values(array_filter($this->ler(), fn(array $leitura): bool => $this->pertence($leitura, $residenciaId)));
        usort($leituras, fn(array $a, array $b): int => strcmp($b['data'], $a['data']));
        return [
            'data' => array_slice($leituras, ($pagina - 1) * $limite, $limite),
            'total' => count($leituras),
        ];
    }

    public function todas(int $residenciaId): array
    {
        return array_values(array_filter($this->ler(), fn(array $leitura): bool => $this->pertence($leitura, $residenciaId)));
    }

    public function todasAdministracao(): array
    {
        return $this->ler();
    }

    public function proximoIdentificador(array $leituras): int
    {
        $ids = array_column($leituras, 'identificador');
        return $ids ? max($ids) + 1 : 1;
    }

    public function adicionar(array $leitura, int $residenciaId): array
    {
        $leituras = $this->ler();
        $leitura['identificador'] = $this->proximoIdentificador($leituras);
        $leitura['residencia_id'] = $residenciaId;
        $leituras[] = $leitura;
        $this->gravar($leituras);
        return $leitura;
    }

    public function encontrarPorData(string $data, int $residenciaId): ?array
    {
        foreach ($this->ler() as $leitura) {
            if (($leitura['data'] ?? '') === $data && $this->pertence($leitura, $residenciaId)) return $leitura;
        }
        return null;
    }

    public function atualizar(int $id, int $residenciaId, callable $alteracao): ?array
    {
        $leituras = $this->ler();
        foreach ($leituras as &$leitura) {
            if (($leitura['identificador'] ?? 0) !== $id || !$this->pertence($leitura, $residenciaId)) continue;
            $leitura = $alteracao($leitura);
            $this->gravar($leituras);
            return $leitura;
        }
        return null;
    }

    public function excluir(int $id, int $residenciaId): ?array
    {
        $leituras = $this->ler();
        foreach ($leituras as $indice => $leitura) {
            if (($leitura['identificador'] ?? 0) !== $id || !$this->pertence($leitura, $residenciaId)) continue;
            array_splice($leituras, $indice, 1);
            $this->gravar($leituras);
            return $leitura;
        }
        return null;
    }

    private function pertence(array $leitura, int $residenciaId): bool
    {
        return (int) ($leitura['residencia_id'] ?? 1) === $residenciaId;
    }
}

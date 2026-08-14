<?php
declare(strict_types=1);

namespace App\Infraestrutura\Persistencia;

final class RepositorioLeituras extends RepositorioJson
{
    public function paginar(int $pagina, int $limite): array
    {
        $leituras = $this->ler();
        usort($leituras, fn(array $a, array $b): int => strcmp($b['data'], $a['data']));
        return [
            'data' => array_slice($leituras, ($pagina - 1) * $limite, $limite),
            'total' => count($leituras),
        ];
    }

    public function todas(): array
    {
        return $this->ler();
    }

    public function proximoIdentificador(array $leituras): int
    {
        $ids = array_column($leituras, 'identificador');
        return $ids ? max($ids) + 1 : 1;
    }

    public function adicionar(array $leitura): array
    {
        $leituras = $this->ler();
        $leitura['identificador'] = $this->proximoIdentificador($leituras);
        $leituras[] = $leitura;
        $this->gravar($leituras);
        return $leitura;
    }

    public function encontrarPorData(string $data): ?array
    {
        foreach ($this->ler() as $leitura) {
            if (($leitura['data'] ?? '') === $data) return $leitura;
        }
        return null;
    }

    public function atualizar(int $id, callable $alteracao): ?array
    {
        $leituras = $this->ler();
        foreach ($leituras as &$leitura) {
            if (($leitura['identificador'] ?? 0) !== $id) continue;
            $leitura = $alteracao($leitura);
            $this->gravar($leituras);
            return $leitura;
        }
        return null;
    }

    public function excluir(int $id): ?array
    {
        $leituras = $this->ler();
        foreach ($leituras as $indice => $leitura) {
            if (($leitura['identificador'] ?? 0) !== $id) continue;
            array_splice($leituras, $indice, 1);
            $this->gravar($leituras);
            return $leitura;
        }
        return null;
    }
}

<?php
declare(strict_types=1);

namespace App\Infraestrutura\Persistencia;

use RuntimeException;

abstract class RepositorioJson
{
    protected string $arquivo;

    public function __construct(string $arquivo)
    {
        $this->arquivo = $arquivo;
    }

    protected function ler(): array
    {
        $conteudo = @file_get_contents($this->arquivo);
        if ($conteudo === false) throw new RuntimeException('Não foi possível ler os dados da aplicação.');
        $dados = json_decode($conteudo, true);
        if (!is_array($dados)) throw new RuntimeException('O arquivo de dados está inválido.');
        return $dados;
    }

    protected function gravar(array $dados): void
    {
        $temporario = $this->arquivo . '.tmp';
        $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false || file_put_contents($temporario, $json, LOCK_EX) === false || !rename($temporario, $this->arquivo)) {
            @unlink($temporario);
            throw new RuntimeException('Não foi possível salvar os dados da aplicação.');
        }
    }
}

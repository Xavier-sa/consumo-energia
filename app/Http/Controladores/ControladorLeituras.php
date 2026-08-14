<?php
declare(strict_types=1);

namespace App\Http\Controladores;

use App\Aplicacao\ServicoLeituras;
use App\Http\Requisicao;
use App\Http\Resposta;

final class ControladorLeituras
{
    private ServicoLeituras $servico;

    public function __construct(ServicoLeituras $servico)
    {
        $this->servico = $servico;
    }

    public function listar(): void
    {
        Resposta::json($this->servico->listar((int) ($_GET['page'] ?? 1), (int) ($_GET['limit'] ?? 10), $this->residenciaId()));
    }

    public function criar(): void
    {
        Resposta::json(['ok' => true, 'item' => $this->servico->criar(Requisicao::corpo(), $this->residenciaId())]);
    }

    public function atualizar(): void
    {
        $leitura = $this->servico->atualizar((int) ($_GET['id'] ?? 0), Requisicao::corpo(), $this->residenciaId());
        if (!$leitura) {
            Resposta::json(['message' => 'Leitura não encontrada.'], 404);
            return;
        }
        Resposta::json(['ok' => true, 'item' => $leitura]);
    }

    public function excluir(): void
    {
        if (!$this->servico->excluir((int) ($_GET['id'] ?? 0), $this->residenciaId())) {
            Resposta::json(['message' => 'Leitura não encontrada.'], 404);
            return;
        }
        Resposta::json(['ok' => true]);
    }

    public function exportar(): void
    {
        Resposta::csv($this->servico->todas($this->residenciaId()));
    }

    private function residenciaId(): int
    {
        return (int) ($_SESSION['user']['residencia_id'] ?? 1);
    }
}

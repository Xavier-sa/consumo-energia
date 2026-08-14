<?php
declare(strict_types=1);

namespace App\Http\Controladores;

use App\Aplicacao\ServicoAdministracao;
use App\Http\Resposta;

final class ControladorAdministracao
{
    private ServicoAdministracao $servico;

    public function __construct(ServicoAdministracao $servico)
    {
        $this->servico = $servico;
    }

    public function painel(): void
    {
        if (($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            Resposta::json(['message' => 'Você não tem permissão para acessar o painel administrativo.'], 403);
            return;
        }
        Resposta::json($this->servico->painel());
    }
}

<?php
declare(strict_types=1);

namespace App\Http;

final class Resposta
{
    public static function json(array $dados, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function csv(array $leituras): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="historico-de-consumo.csv"');
        $saida = fopen('php://output', 'w');
        fputcsv($saida, ['identificador', 'data', 'leitura_manha', 'leitura_noite', 'consumo']);
        foreach ($leituras as $leitura) {
            fputcsv($saida, [
                $leitura['identificador'], $leitura['data'], $leitura['leitura_manha'] ?? '',
                $leitura['leitura_noite'] ?? '', $leitura['consumo'] ?? '',
            ]);
        }
        fclose($saida);
    }
}

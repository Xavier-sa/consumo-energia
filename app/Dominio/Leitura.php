<?php
declare(strict_types=1);

namespace App\Dominio;

use DomainException;

final class Leitura
{
    public static function validar(string $data, int $manha, int $noite): void
    {
        $dataValida = \DateTimeImmutable::createFromFormat('!Y-m-d', $data);
        if (!$dataValida || $dataValida->format('Y-m-d') !== $data) {
            throw new DomainException('Informe uma data válida.');
        }
        if ($manha < 0 || $noite < 0) {
            throw new DomainException('As leituras não podem ser negativas.');
        }
        if ($noite < $manha) {
            throw new DomainException('A leitura da noite deve ser igual ou maior que a da manhã.');
        }
    }

    public static function calcularConsumo(int $manha, int $noite): int
    {
        return $noite - $manha;
    }
}

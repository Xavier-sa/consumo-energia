<?php
declare(strict_types=1);

namespace App\Dominio;

use DomainException;

final class Leitura
{
    public static function validar(string $data, ?int $manha, ?int $noite): void
    {
        $dataValida = \DateTimeImmutable::createFromFormat('!Y-m-d', $data);
        if (!$dataValida || $dataValida->format('Y-m-d') !== $data) {
            throw new DomainException('Informe uma data válida.');
        }
        if ($manha === null && $noite === null) {
            throw new DomainException('Informe a leitura da manhã ou da noite.');
        }
        if (($manha !== null && $manha < 0) || ($noite !== null && $noite < 0)) {
            throw new DomainException('As leituras não podem ser negativas.');
        }
        if ($manha !== null && $noite !== null && $noite < $manha) {
            throw new DomainException('A leitura da noite deve ser igual ou maior que a da manhã.');
        }
    }

    public static function calcularConsumo(?int $manha, ?int $noite): ?int
    {
        if ($manha === null || $noite === null) return null;
        return $noite - $manha;
    }
}

<?php
declare(strict_types=1);

namespace App\Dominio;

use DomainException;

final class Usuario
{
    public static function validar(string $nome, string $senha): void
    {
        if (!preg_match('/^[A-Za-z0-9._-]{3,30}$/', $nome)) {
            throw new DomainException('O usuário deve ter de 3 a 30 caracteres e usar apenas letras, números, ponto, hífen ou sublinhado.');
        }
        $tamanho = strlen($senha);
        if ($tamanho < 8 || $tamanho > 72) {
            throw new DomainException('A senha deve ter de 8 a 72 caracteres.');
        }
    }
}

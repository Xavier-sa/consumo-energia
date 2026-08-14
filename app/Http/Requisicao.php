<?php
declare(strict_types=1);

namespace App\Http;

final class Requisicao
{
    public static function corpo(): array
    {
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false) return $_POST;
        $corpo = json_decode(file_get_contents('php://input'), true);
        return is_array($corpo) ? $corpo : [];
    }

    public static function ip(): string
    {
        $encaminhado = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($encaminhado) return trim(explode(',', $encaminhado)[0]);
        return $_SERVER['REMOTE_ADDR'] ?? 'desconhecido';
    }
}

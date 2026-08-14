<?php
declare(strict_types=1);

$arquivosDeDados = [
    __DIR__ . '/data/usuarios.json' => __DIR__ . '/data/usuarios.exemplo.json',
    __DIR__ . '/data/consumo.json' => __DIR__ . '/data/consumo.exemplo.json',
];

foreach ($arquivosDeDados as $destino => $exemplo) {
    if (!is_file($destino) && (!is_file($exemplo) || !copy($exemplo, $destino))) {
        throw new RuntimeException('Não foi possível criar o arquivo de dados: ' . basename($destino));
    }
}

spl_autoload_register(function (string $classe): void {
    $prefixo = 'App\\';
    if (strpos($classe, $prefixo) !== 0) return;

    $relativo = substr($classe, strlen($prefixo));
    $arquivo = __DIR__ . '/app/' . str_replace('\\', '/', $relativo) . '.php';
    if (is_file($arquivo)) require $arquivo;
});

return [
    'dados' => __DIR__ . '/data',
    'usuarios' => __DIR__ . '/data/usuarios.json',
    'consumo' => __DIR__ . '/data/consumo.json',
    'uploads' => __DIR__ . '/public/uploads',
    'url_uploads' => 'uploads/',
];

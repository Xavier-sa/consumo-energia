<?php
declare(strict_types=1);

namespace App\Infraestrutura\Arquivos;

use RuntimeException;

final class GerenciadorFotos
{
    private string $diretorio;
    private string $urlBase;

    public function __construct(string $diretorio, string $urlBase)
    {
        $this->diretorio = $diretorio;
        $this->urlBase = $urlBase;
        if (!is_dir($diretorio) && !mkdir($diretorio, 0755, true) && !is_dir($diretorio)) {
            throw new RuntimeException('Não foi possível preparar o diretório de fotos.');
        }
    }

    public function salvar(string $campo): ?string
    {
        if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) return null;
        $arquivo = $_FILES[$campo];
        if ($arquivo['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Não foi possível enviar a foto.');
        if ($arquivo['size'] > 5 * 1024 * 1024) throw new RuntimeException('A foto deve ter no máximo 5 MB.');

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
        $extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensoes[$mime])) throw new RuntimeException('Use uma imagem JPG, PNG ou WebP.');

        $nome = bin2hex(random_bytes(16)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($arquivo['tmp_name'], $this->diretorio . '/' . $nome)) {
            throw new RuntimeException('Não foi possível guardar a foto.');
        }
        return $this->urlBase . $nome;
    }

    public function excluir(?string $url): void
    {
        if (!$url || basename($url) !== str_replace($this->urlBase, '', $url)) return;
        $arquivo = $this->diretorio . '/' . basename($url);
        if (is_file($arquivo)) unlink($arquivo);
    }
}

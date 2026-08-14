<?php
declare(strict_types=1);

namespace App\Aplicacao;

use App\Dominio\Leitura;
use App\Infraestrutura\Arquivos\GerenciadorFotos;
use App\Infraestrutura\Persistencia\RepositorioLeituras;

final class ServicoLeituras
{
    private RepositorioLeituras $repositorio;
    private GerenciadorFotos $fotos;

    public function __construct(RepositorioLeituras $repositorio, GerenciadorFotos $fotos)
    {
        $this->repositorio = $repositorio;
        $this->fotos = $fotos;
    }

    public function listar(int $pagina, int $limite): array
    {
        return $this->repositorio->paginar(max(1, $pagina), max(1, min(50, $limite)));
    }

    public function criar(array $dados): array
    {
        [$data, $manha, $noite] = $this->dadosBasicos($dados);
        $fotoManha = $this->fotos->salvar('morning_photo');
        try {
            $fotoNoite = $this->fotos->salvar('night_photo');
            return $this->repositorio->adicionar($this->montar($data, $manha, $noite, $fotoManha, $fotoNoite, $dados));
        } catch (\Throwable $erro) {
            $this->fotos->excluir($fotoManha);
            throw $erro;
        }
    }

    public function atualizar(int $id, array $dados): ?array
    {
        [$data, $manha, $noite] = $this->dadosBasicos($dados);
        $novaFotoManha = $this->fotos->salvar('morning_photo');
        try {
            $novaFotoNoite = $this->fotos->salvar('night_photo');
            $anterior = null;
            $atualizada = $this->repositorio->atualizar($id, function (array $leitura) use ($data, $manha, $noite, $novaFotoManha, $novaFotoNoite, $dados, &$anterior): array {
                $anterior = $leitura;
                $leitura['data'] = $data;
                $leitura['leitura_manha'] = $manha;
                $leitura['leitura_noite'] = $noite;
                $leitura['consumo'] = Leitura::calcularConsumo($manha, $noite);
                if ($novaFotoManha) {
                    $leitura['foto_manha'] = $novaFotoManha;
                    $leitura['horario_foto_manha'] = $this->horario($dados['morning_photo_time'] ?? null);
                }
                if ($novaFotoNoite) {
                    $leitura['foto_noite'] = $novaFotoNoite;
                    $leitura['horario_foto_noite'] = $this->horario($dados['night_photo_time'] ?? null);
                }
                return $leitura;
            });
            if (!$atualizada) {
                $this->fotos->excluir($novaFotoManha);
                $this->fotos->excluir($novaFotoNoite);
                return null;
            }
            if ($novaFotoManha) $this->fotos->excluir($anterior['foto_manha'] ?? null);
            if ($novaFotoNoite) $this->fotos->excluir($anterior['foto_noite'] ?? null);
            return $atualizada;
        } catch (\Throwable $erro) {
            $this->fotos->excluir($novaFotoManha);
            throw $erro;
        }
    }

    public function excluir(int $id): bool
    {
        $leitura = $this->repositorio->excluir($id);
        if (!$leitura) return false;
        $this->fotos->excluir($leitura['foto_manha'] ?? null);
        $this->fotos->excluir($leitura['foto_noite'] ?? null);
        return true;
    }

    public function todas(): array
    {
        return $this->repositorio->todas();
    }

    private function dadosBasicos(array $dados): array
    {
        $data = (string) ($dados['date'] ?? date('Y-m-d'));
        $manha = (int) ($dados['morning'] ?? 0);
        $noite = (int) ($dados['night'] ?? 0);
        Leitura::validar($data, $manha, $noite);
        return [$data, $manha, $noite];
    }

    private function montar(string $data, int $manha, int $noite, ?string $fotoManha, ?string $fotoNoite, array $dados): array
    {
        return [
            'data' => $data,
            'leitura_manha' => $manha,
            'leitura_noite' => $noite,
            'consumo' => Leitura::calcularConsumo($manha, $noite),
            'foto_manha' => $fotoManha,
            'horario_foto_manha' => $fotoManha ? $this->horario($dados['morning_photo_time'] ?? null) : null,
            'foto_noite' => $fotoNoite,
            'horario_foto_noite' => $fotoNoite ? $this->horario($dados['night_photo_time'] ?? null) : null,
        ];
    }

    private function horario(?string $valor): string
    {
        $timestamp = $valor ? strtotime($valor) : false;
        return date('c', $timestamp === false ? time() : $timestamp);
    }
}

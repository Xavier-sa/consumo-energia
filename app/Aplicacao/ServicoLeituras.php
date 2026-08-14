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

    public function listar(int $pagina, int $limite, int $residenciaId): array
    {
        return $this->repositorio->paginar(max(1, $pagina), max(1, min(50, $limite)), $residenciaId);
    }

    public function criar(array $dados, int $residenciaId): array
    {
        [$data, $turno, $manha, $noite] = $this->dadosBasicos($dados);
        $existente = $this->repositorio->encontrarPorData($data, $residenciaId);
        if ($existente) {
            $turnoPreenchido = $turno === 'morning' ? ($existente['leitura_manha'] ?? null) : ($existente['leitura_noite'] ?? null);
            if ($turnoPreenchido !== null) {
                throw new \DomainException('Esse turno já foi registrado nessa data. Use Editar para alterá-lo.');
            }
            return $this->atualizar((int) $existente['identificador'], $dados, $residenciaId);
        }

        $fotoManha = $turno === 'morning' ? $this->fotos->salvar('morning_photo') : null;
        try {
            $fotoNoite = $turno === 'night' ? $this->fotos->salvar('night_photo') : null;
            return $this->repositorio->adicionar($this->montar($data, $manha, $noite, $fotoManha, $fotoNoite, $dados), $residenciaId);
        } catch (\Throwable $erro) {
            $this->fotos->excluir($fotoManha);
            throw $erro;
        }
    }

    public function atualizar(int $id, array $dados, int $residenciaId): ?array
    {
        [$data, $turno, $manha, $noite] = $this->dadosBasicos($dados);
        $novaFotoManha = $turno === 'morning' ? $this->fotos->salvar('morning_photo') : null;
        $novaFotoNoite = null;
        try {
            $novaFotoNoite = $turno === 'night' ? $this->fotos->salvar('night_photo') : null;
            $anterior = null;
            $atualizada = $this->repositorio->atualizar($id, $residenciaId, function (array $leitura) use ($data, $turno, $manha, $noite, $novaFotoManha, $novaFotoNoite, $dados, &$anterior): array {
                $anterior = $leitura;
                $leitura['data'] = $data;
                if ($turno === 'morning') $leitura['leitura_manha'] = $manha;
                if ($turno === 'night') $leitura['leitura_noite'] = $noite;
                Leitura::validar($data, $leitura['leitura_manha'] ?? null, $leitura['leitura_noite'] ?? null);
                $leitura['consumo'] = Leitura::calcularConsumo($leitura['leitura_manha'] ?? null, $leitura['leitura_noite'] ?? null);
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
            $this->fotos->excluir($novaFotoNoite);
            throw $erro;
        }
    }

    public function excluir(int $id, int $residenciaId): bool
    {
        $leitura = $this->repositorio->excluir($id, $residenciaId);
        if (!$leitura) return false;
        $this->fotos->excluir($leitura['foto_manha'] ?? null);
        $this->fotos->excluir($leitura['foto_noite'] ?? null);
        return true;
    }

    public function todas(int $residenciaId): array
    {
        return $this->repositorio->todas($residenciaId);
    }

    private function dadosBasicos(array $dados): array
    {
        $data = (string) ($dados['date'] ?? date('Y-m-d'));
        $turno = (string) ($dados['shift'] ?? '');
        if (!in_array($turno, ['morning', 'night'], true)) {
            throw new \DomainException('Escolha o turno da manhã ou da noite.');
        }
        $manha = $turno === 'morning' && array_key_exists('morning', $dados) ? (int) $dados['morning'] : null;
        $noite = $turno === 'night' && array_key_exists('night', $dados) ? (int) $dados['night'] : null;
        Leitura::validar($data, $manha, $noite);
        return [$data, $turno, $manha, $noite];
    }

    private function montar(string $data, ?int $manha, ?int $noite, ?string $fotoManha, ?string $fotoNoite, array $dados): array
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

<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use App\Aplicacao\ServicoAutenticacao;
use App\Aplicacao\ServicoAdministracao;
use App\Aplicacao\ServicoLeituras;
use App\Dominio\Usuario;
use App\Infraestrutura\Arquivos\GerenciadorFotos;
use App\Infraestrutura\Persistencia\RepositorioLeituras;
use App\Infraestrutura\Persistencia\RepositorioUsuarios;

function afirmar(bool $condicao, string $mensagem): void
{
    if (!$condicao) throw new RuntimeException($mensagem);
}

function executar(string $nome, callable $teste): void
{
    $teste();
    echo "✓ {$nome}\n";
}

$base = sys_get_temp_dir() . '/consumo-energisa-' . bin2hex(random_bytes(6));
mkdir($base, 0700, true);
$usuariosArquivo = $base . '/usuarios.json';
$leiturasArquivo = $base . '/consumo.json';
file_put_contents($usuariosArquivo, json_encode([
    ['username' => 'DARA', 'password' => 'senha-legada', 'accesses' => []],
    ['username' => 'XAVIER', 'password' => 'outra-senha', 'accesses' => []],
]));
file_put_contents($leiturasArquivo, json_encode([
    ['identificador' => 1, 'data' => '2026-08-13', 'leitura_manha' => 515, 'leitura_noite' => 518, 'consumo' => 3],
    ['identificador' => 2, 'residencia_id' => 2, 'data' => '2026-08-13', 'leitura_manha' => 100, 'leitura_noite' => null, 'consumo' => null],
]));

try {
    $autenticacao = new ServicoAutenticacao(new RepositorioUsuarios($usuariosArquivo));

    executar('login legado preserva a residência inicial e converte a senha', function () use ($autenticacao, $usuariosArquivo): void {
        $sessao = $autenticacao->entrar('dara', 'senha-legada', '127.0.0.1');
        afirmar(($sessao['residencia_id'] ?? null) === 1, 'Usuário legado deveria pertencer à residência 1.');
        $usuarios = json_decode((string) file_get_contents($usuariosArquivo), true);
        afirmar(isset($usuarios[0]['password_hash']), 'A senha deveria ter sido convertida para hash.');
        afirmar(!isset($usuarios[0]['password']), 'A senha em texto simples deveria ter sido removida.');
        afirmar(password_verify('senha-legada', $usuarios[0]['password_hash']), 'O hash deveria validar a senha original.');
    });

    executar('cadastro cria conta em residência isolada', function () use ($autenticacao, $usuariosArquivo): void {
        $sessao = $autenticacao->cadastrar('NOVA.PESSOA', 'senha-segura', '127.0.0.2', true);
        afirmar(($sessao['residencia_id'] ?? null) === 2, 'A nova conta deveria receber a residência 2.');
        $usuarios = json_decode((string) file_get_contents($usuariosArquivo), true);
        $novo = $usuarios[count($usuarios) - 1];
        afirmar(!isset($novo['password']), 'Cadastro não deve armazenar senha em texto simples.');
        afirmar(password_verify('senha-segura', $novo['password_hash']), 'Cadastro deve armazenar um hash válido.');
        afirmar($sessao['role'] === 'user' && $novo['role'] === 'user', 'Cadastro novo deve permanecer usuário comum.');
        afirmar(isset($novo['termos_aceitos_em']) && strtotime($novo['termos_aceitos_em']) !== false, 'Cadastro deve registrar a data do aceite.');
        afirmar($novo['versao_termos'] === '1.0', 'Cadastro deve registrar a versão dos Termos.');
        afirmar($novo['versao_politica_privacidade'] === '1.0', 'Cadastro deve registrar a versão da Política.');
    });

    executar('cadastro sem aceite é rejeitado sem persistência', function () use ($autenticacao, $usuariosArquivo): void {
        $quantidade = count(json_decode((string) file_get_contents($usuariosArquivo), true));
        try {
            $autenticacao->cadastrar('SEM.ACEITE', 'senha-segura', '127.0.0.7', false);
            afirmar(false, 'Cadastro sem aceite deveria falhar.');
        } catch (DomainException $erro) {
            afirmar(strpos($erro->getMessage(), 'leia e aceite') !== false, 'A falha deveria explicar o aceite necessário.');
        }
        afirmar(count(json_decode((string) file_get_contents($usuariosArquivo), true)) === $quantidade, 'Cadastro sem aceite não deve alterar o JSON.');
    });

    executar('Xavier legado recebe e persiste o papel de administrador', function () use ($autenticacao, $usuariosArquivo): void {
        $sessao = $autenticacao->entrar('XAVIER', 'outra-senha', '127.0.0.5');
        afirmar($sessao['role'] === 'admin', 'Xavier deveria receber o papel admin.');
        $usuarios = json_decode((string) file_get_contents($usuariosArquivo), true);
        afirmar($usuarios[1]['role'] === 'admin', 'O papel admin deveria ser persistido no JSON.');
    });

    executar('cadastro duplicado é rejeitado sem gravar usuário', function () use ($autenticacao, $usuariosArquivo): void {
        $quantidade = count(json_decode((string) file_get_contents($usuariosArquivo), true));
        try {
            $autenticacao->cadastrar('nova.pessoa', 'outra-senha', '127.0.0.3', true);
            afirmar(false, 'O cadastro duplicado deveria falhar.');
        } catch (DomainException $erro) {
            afirmar($erro->getMessage() === 'Não foi possível criar a conta com esses dados.', 'A falha deveria usar mensagem genérica.');
        }
        afirmar(count(json_decode((string) file_get_contents($usuariosArquivo), true)) === $quantidade, 'Cadastro duplicado não deve alterar o JSON.');
    });

    $leituras = new ServicoLeituras(
        new RepositorioLeituras($leiturasArquivo),
        new GerenciadorFotos($base . '/uploads', 'uploads/')
    );

    executar('painel administrativo agrega usuários e leituras sem segredos', function () use ($usuariosArquivo, $leiturasArquivo): void {
        $painel = (new ServicoAdministracao(new RepositorioUsuarios($usuariosArquivo), new RepositorioLeituras($leiturasArquivo)))->painel();
        afirmar(count($painel['usuarios']) === 3, 'O painel deveria listar os três usuários.');
        afirmar(count($painel['leituras']) === 2, 'O painel deveria listar todas as leituras.');
        afirmar($painel['leituras'][0]['usuarios'] === ['DARA', 'XAVIER'], 'A leitura legada deveria indicar os moradores da residência inicial.');
        $json = json_encode($painel);
        afirmar(strpos($json, 'password') === false && strpos($json, 'last_ip') === false && strpos($json, '127.0.0') === false && strpos($json, 'accesses') === false, 'O painel não deve expor credenciais, acessos ou IPs.');
    });

    executar('variação do nome Xavier não recebe poder administrativo', function () use ($autenticacao): void {
        $sessao = $autenticacao->cadastrar('XAVIER.NOVO', 'senha-segura', '127.0.0.6', true);
        afirmar($sessao['role'] === 'user', 'Uma conta nova com variação do nome Xavier deve ser comum.');
    });

    executar('leituras legadas pertencem somente à residência inicial', function () use ($leituras): void {
        $inicial = $leituras->listar(1, 10, 1);
        $nova = $leituras->listar(1, 10, 2);
        afirmar($inicial['total'] === 1 && $inicial['data'][0]['identificador'] === 1, 'A residência inicial deveria ver o dado legado.');
        afirmar($nova['total'] === 1 && $nova['data'][0]['identificador'] === 2, 'A residência 2 deveria ver apenas seu dado.');
    });

    executar('alteração e exclusão cruzadas são bloqueadas', function () use ($leituras): void {
        $alterada = $leituras->atualizar(1, ['date' => '2026-08-13', 'shift' => 'night', 'night' => '519'], 2);
        afirmar($alterada === null, 'Outra residência não deveria atualizar a leitura.');
        afirmar($leituras->excluir(1, 2) === false, 'Outra residência não deveria excluir a leitura.');
        afirmar($leituras->listar(1, 10, 1)['data'][0]['leitura_noite'] === 518, 'O dado legado deveria permanecer intacto.');
    });

    executar('nova leitura recebe a residência da sessão', function () use ($leituras): void {
        $criada = $leituras->criar(['date' => '2026-08-14', 'shift' => 'morning', 'morning' => '521'], 2);
        afirmar($criada['residencia_id'] === 2, 'A leitura deveria receber a residência 2.');
        afirmar($leituras->listar(1, 10, 1)['total'] === 1, 'A residência inicial não deveria ver a nova leitura.');
        afirmar($leituras->listar(1, 10, 2)['total'] === 2, 'A residência 2 deveria ver a nova leitura.');
    });

    executar('validação aplica limites de usuário e senha', function () use ($autenticacao): void {
        foreach ([['AB', 'senha-forte'], ['USUARIO', '1234567']] as [$usuario, $senha]) {
            try {
                $autenticacao->cadastrar($usuario, $senha, '127.0.0.4', true);
                afirmar(false, 'Dados fora dos limites deveriam falhar.');
            } catch (DomainException $erro) {
                afirmar($erro->getMessage() !== '', 'A validação deveria explicar o limite inválido.');
            }
        }
        Usuario::validar('ABC', '12345678');
    });

    executar('interface oferece login e cadastro com usuário livre', function (): void {
        $pagina = (string) file_get_contents(dirname(__DIR__) . '/views/paginas/inicial.php');
        afirmar(strpos($pagina, 'id="show-login"') !== false, 'A interface deveria oferecer a aba Entrar.');
        afirmar(strpos($pagina, 'id="show-register"') !== false, 'A interface deveria oferecer a aba Criar conta.');
        afirmar(strpos($pagina, '<input id="username"') !== false, 'O login deveria usar um campo livre de usuário.');
        afirmar(strpos($pagina, '<select id="username"') === false, 'O login não deveria limitar usuários a um seletor fixo.');
        afirmar(strpos($pagina, 'id="register-legal-acceptance"') !== false, 'O cadastro deveria exigir aceite dos documentos.');
        afirmar(strpos($pagina, 'data-legal-document="terms-dialog"') !== false, 'O cadastro deveria oferecer os Termos de Uso.');
        afirmar(strpos($pagina, 'data-legal-document="privacy-dialog"') !== false, 'O cadastro deveria oferecer a Política de Privacidade.');
    });

    executar('README divulga a aplicação sem webhook', function (): void {
        $readme = (string) file_get_contents(dirname(__DIR__) . '/README.md');
        afirmar(strpos($readme, 'https://consumo-energisa.wasxtech.com.br/public/') !== false, 'O README deveria conter a URL pública.');
        afirmar(strpos($readme, 'webhooks.hostinger.com') === false, 'O README não deve conter webhook de deploy.');
    });

    echo "Todos os testes passaram.\n";
} finally {
    foreach (glob($base . '/uploads/*') ?: [] as $arquivo) unlink($arquivo);
    if (is_dir($base . '/uploads')) rmdir($base . '/uploads');
    if (is_file($usuariosArquivo)) unlink($usuariosArquivo);
    if (is_file($leiturasArquivo)) unlink($leiturasArquivo);
    if (is_dir($base)) rmdir($base);
}

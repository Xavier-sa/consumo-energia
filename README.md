# Meu Consumo

Aplicação web simples para registrar leituras de energia pela manhã e à noite.
O sistema calcula o consumo diário, mantém um histórico e permite anexar fotos
do medidor com o horário do registro.

O projeto utiliza PHP puro, JavaScript com módulos ES, CSS e arquivos JSON. Não
é necessário instalar Composer, Node.js, banco de dados ou dependências externas.

## Recursos

- Autenticação com sessão PHP.
- Registro das leituras da manhã e da noite.
- Cálculo automático do consumo diário.
- Fotos opcionais para cada período.
- Registro do horário das fotos.
- Edição e exclusão de leituras.
- Histórico paginado.
- Exportação do histórico em CSV.
- Interface responsiva para computador e celular.
- Estrutura MVC com componentes reutilizáveis.

## Requisitos

- PHP 7.4 ou superior.
- Extensão PHP `fileinfo` habilitada para validar imagens.
- Sessões PHP habilitadas.
- Permissão de escrita nos arquivos de dados e no diretório de uploads.
- Servidor Apache, Nginx ou hospedagem compatível com PHP.

## Executar localmente

1. Abra um terminal na raiz do projeto.
2. Inicie o servidor PHP:

   ```bash
   php -S localhost:8000 -t public
   ```

3. Acesse `http://localhost:8000` no navegador.

Na primeira execução, o sistema cria automaticamente:

- `data/usuarios.json`, usando `data/usuarios.exemplo.json` como modelo.
- `data/consumo.json`, usando `data/consumo.exemplo.json` como modelo.

As credenciais iniciais do arquivo de exemplo são:

- Usuário: `USUARIO`
- Senha: `ALTERE_ESTA_SENHA`

Altere essas credenciais em `data/usuarios.json` antes de disponibilizar o
sistema na internet.

## Publicar em uma hospedagem

### Opção recomendada: usar `public/` como raiz pública

Configure o documento raiz do domínio para apontar para o diretório `public/`.
Mantenha `app/`, `data/`, `views/` e `bootstrap.php` fora do acesso público.

Essa configuração oferece a melhor proteção porque os arquivos JSON e o código
interno não ficam acessíveis diretamente pelo navegador.

### Opção compatível: publicar o projeto completo

Se a hospedagem não permite alterar a raiz pública, envie todo o projeto para o
diretório do domínio. O `index.php` da raiz encaminha o acesso para `public/`.

Em servidores Apache, `data/.htaccess` bloqueia o acesso direto aos JSON, e
`public/uploads/.htaccess` impede a execução de scripts enviados como arquivos.
Em Nginx ou outro servidor, crie regras equivalentes antes de publicar.

## Permissões de escrita

O usuário que executa o PHP precisa gravar nestes locais:

```text
data/usuarios.json
data/consumo.json
public/uploads/
```

Se os JSON ainda não existirem, o PHP também precisa de permissão para criar
arquivos dentro de `data/`.

Evite conceder permissão `777`. Prefira definir o proprietário e o grupo usados
pelo processo PHP e liberar somente a escrita necessária.

## Dados locais e Git

O `.gitignore` exclui do versionamento:

- `data/usuarios.json`, que contém credenciais e histórico de acessos.
- `data/consumo.json`, que contém as leituras reais.
- Imagens armazenadas em `public/uploads/`.
- Arquivos temporários, logs e configurações locais de editores.

Os arquivos abaixo permanecem no repositório como modelos seguros:

- `data/usuarios.exemplo.json`
- `data/consumo.exemplo.json`
- `public/uploads/.gitkeep`
- `public/uploads/.htaccess`

Para redefinir uma instalação local, faça uma cópia de segurança e remova os
JSON reais. Na próxima execução, o sistema recria os arquivos usando os modelos.

## Estrutura do projeto

```text
.
├── app/
│   ├── Aplicacao/             # Casos de uso e coordenação
│   ├── Dominio/               # Regras de negócio
│   ├── Http/                  # Requisição, resposta e controladores
│   └── Infraestrutura/        # Persistência JSON e arquivos
├── data/
│   ├── consumo.exemplo.json
│   └── usuarios.exemplo.json
├── public/
│   ├── assets/
│   │   ├── css/               # Estilos da interface
│   │   └── js/                # API, componentes e utilitários
│   ├── uploads/               # Fotos enviadas
│   ├── api.php                # Roteamento da API
│   └── index.php              # Entrada pública da interface
├── views/
│   ├── componentes/           # Botões e campos reutilizáveis
│   └── paginas/               # Composição das páginas
├── bootstrap.php              # Autoload e configuração
└── index.php                  # Entrada para hospedagens simples
```

## Documentação de engenharia

Os documentos de análise e modelagem ficam em [`docs/`](docs/README.md):

- [Levantamento de requisitos](docs/requisitos.md).
- [Modelo de dados e DER](docs/modelo-de-dados.md).
- [Modelagem UML](docs/uml.md).

## Organização MVC

- **Model:** `app/Dominio`, `app/Aplicacao` e `app/Infraestrutura` concentram
  regras, casos de uso e persistência.
- **View:** `views/` organiza páginas e componentes PHP. Os componentes de
  comportamento do navegador ficam em `public/assets/js/componentes`.
- **Controller:** `app/Http/Controladores` recebe as ações HTTP e chama os
  serviços da aplicação.

O arquivo `public/api.php` funciona como ponto de composição e roteador. Ele não
contém regras de consumo nem acessa os JSON diretamente.

## Formatos aceitos para fotos

- JPEG
- PNG
- WebP
- Tamanho máximo de 5 MB por foto

O backend confere o tipo real do arquivo com `fileinfo`, gera um nome aleatório
e armazena a imagem em `public/uploads/`.

## Segurança antes da publicação

Antes de publicar:

1. Altere o usuário e a senha padrão em `data/usuarios.json`.
2. Confirme que os arquivos JSON reais não estão no Git.
3. Configure `public/` como raiz pública, quando possível.
4. Restrinja o acesso direto ao diretório `data/`.
5. Confirme que scripts não podem ser executados em `public/uploads/`.
6. Ative HTTPS no domínio.
7. Faça cópias de segurança periódicas de `data/` e `public/uploads/`.

> A versão atual armazena senhas em texto simples. Para uma publicação exposta
> a usuários externos, migre as senhas para `password_hash()` e
> `password_verify()` antes de usar o sistema em produção.

## Integração contínua

O workflow `.github/workflows/ci.yml` executa automaticamente em cada envio e
pull request. Ele possui dois jobs:

- **Padrão e integridade:** valida PHP, JavaScript, JSON, conflitos, espaços
  finais e arquivos obsoletos.
- **Segurança e dados sensíveis:** impede JSONs reais, fotos, segredos, scripts
  em uploads, JavaScript perigoso e credenciais não sanitizadas no exemplo.

Execute as mesmas verificações antes de enviar alterações:

```bash
bash tools/ci/verificar-padrao.sh
bash tools/ci/verificar-seguranca.sh
```

Configure a proteção da branch principal no GitHub para exigir os checks
**Padrão e integridade** e **Segurança e dados sensíveis** antes do merge.

## Solução de problemas

### A página abre sem estilos

Confirme que `public/assets/css/main.css` foi enviado e que o domínio aponta para
o diretório correto.

### O sistema não cria os JSON

Conceda ao processo PHP permissão de escrita em `data/` e confirme que os
arquivos `.exemplo.json` existem.

### A foto não é enviada

Confirme que:

- A extensão PHP `fileinfo` está habilitada.
- `public/uploads/` permite escrita pelo PHP.
- A imagem está em JPEG, PNG ou WebP.
- A imagem tem no máximo 5 MB.
- Os limites `upload_max_filesize` e `post_max_size` do PHP aceitam 5 MB.

### O login não funciona

Confira os valores de `username` e `password` em `data/usuarios.json`. O arquivo
é criado automaticamente somente quando ainda não existe.

## Licença

Este projeto ainda não possui uma licença definida.
# consumo-energia

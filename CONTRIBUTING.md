# Como contribuir

Obrigado por contribuir com o Meu Consumo. Este projeto registra leituras de
medidores elétricos e mantém contas e históricos separados por residência.

## Executar o projeto localmente

Você precisa do PHP 7.4 ou superior, da extensão `fileinfo`, de sessões PHP e
de permissão de escrita em `data/` e `public/uploads/`.

1. Clone o repositório e abra um terminal na raiz do projeto.
2. Inicie o servidor local:

   ```bash
   php -S localhost:8000 -t public
   ```

3. Acesse `http://localhost:8000`.

O projeto não usa Composer, Node.js ou banco de dados para executar a
aplicação.

## Criar uma branch

Atualize sua cópia local e crie uma branch curta e descritiva a partir da
branch principal. Use um prefixo que indique o tipo de mudança, por exemplo:

```bash
git switch -c feat/aceite-documentos
```

Evite misturar correções ou funcionalidades sem relação na mesma branch.

## Commits

Use mensagens no padrão Conventional Commits:

- `feat:` para uma funcionalidade;
- `fix:` para uma correção;
- `docs:` para documentação;
- `refactor:` para uma mudança interna sem alterar comportamento;
- `test:` para testes;
- `chore:` para manutenção.

Escreva mensagens objetivas, no presente, e mantenha cada commit focado em uma
mudança coerente.

## Pull requests

Antes de abrir um Pull Request (PR):

1. Explique o problema e a solução adotada.
2. Relacione a issue correspondente, quando houver.
3. Descreva como você validou a mudança.
4. Informe impactos em persistência, privacidade ou compatibilidade.
5. Use dados fictícios e sanitizados em exemplos e capturas de tela.
6. Solicite revisão antes de mesclar mudanças sensíveis.

## Dados e privacidade

> **Atenção:** o projeto usa arquivos JSON como persistência. Trate qualquer
> conteúdo real desses arquivos como dado privado.

- Não modifique desnecessariamente arquivos que contenham dados reais.
- Nunca inclua dados pessoais em issues, commits, capturas de tela ou PRs.
- Nunca versione credenciais, senhas, tokens ou chaves.
- Não adicione fotos reais de usuários ou de leituras à documentação.
- Avalie cuidadosamente qualquer mudança na estrutura de persistência e sua
  compatibilidade com registros existentes.
- Não substitua os JSONs reais por dados de teste. Use uma cópia temporária e
  dados fictícios.

Os arquivos `data/usuarios.json`, `data/consumo.json` e os uploads reais ficam
fora do Git. Os arquivos `*.exemplo.json` devem conter somente dados
sanitizados.

## Validações antes do PR

Execute as verificações usadas pela integração contínua:

```bash
bash tools/ci/verificar-padrao.sh
bash tools/ci/verificar-seguranca.sh
```

Confirme também que:

- os fluxos afetados funcionam em desktop e celular;
- o uso por teclado e as mensagens de erro continuam acessíveis;
- usuários e leituras de outras residências permanecem isolados;
- os JSONs reais e uploads não foram alterados;
- o diff não contém dados pessoais nem credenciais.

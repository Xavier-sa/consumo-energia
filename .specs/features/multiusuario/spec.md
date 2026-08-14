# Multiusuário por residência

## Problema

O login atual contém usuários fixos e todas as leituras são globais. A aplicação pública precisa aceitar novas contas sem expor ou alterar o histórico já existente de Dara e Xavier.

## Decisões e premissas

- Dara, Xavier e toda leitura legada sem `residencia_id` pertencem à residência inicial `1`.
- Cada cadastro público cria uma nova residência privada.
- Usuário é único sem diferenciar maiúsculas de minúsculas; e-mail e convite de moradores não fazem parte desta entrega.
- Senhas novas usam hash; credenciais legadas em texto simples são convertidas no primeiro login válido.
- Os JSON reais do servidor não serão substituídos durante o deploy.

## Requisitos e critérios de aceitação

- **MULTI-01 — Cadastro:** QUANDO um visitante informar usuário válido e senha de 8 a 72 caracteres, ENTÃO o sistema DEVE criar uma conta com senha protegida, uma nova residência e iniciar a sessão.
- **MULTI-02 — Compatibilidade:** QUANDO Dara ou Xavier entrar usando um registro legado, ENTÃO o sistema DEVE manter acesso às leituras existentes e converter sua senha para hash sem modificar as leituras.
- **MULTI-03 — Isolamento:** QUANDO um usuário autenticado listar, criar, editar, excluir ou exportar leituras, ENTÃO o sistema DEVE operar somente sobre sua residência.
- **MULTI-04 — Dados legados:** QUANDO uma leitura não possuir `residencia_id`, ENTÃO o sistema DEVE tratá-la como pertencente à residência `1`, inclusive valores `null` de turnos incompletos.
- **MULTI-05 — Interface:** QUANDO a página de acesso abrir, ENTÃO o visitante DEVE poder alternar entre entrar e criar conta usando um campo livre de usuário.
- **MULTI-06 — Divulgação:** QUANDO alguém consultar o README, ENTÃO DEVE encontrar o endereço público da aplicação e nenhum webhook de deploy.

## Validação e falhas

- Usuário: 3 a 30 caracteres, letras, números, ponto, hífen ou sublinhado.
- Senha: 8 a 72 caracteres; confirmação idêntica.
- Cadastro duplicado retorna erro sem criar residência.
- Falha de login usa mensagem genérica.
- Rotas de leituras continuam exigindo sessão; identificadores de outra residência respondem como não encontrados.
- Concorrência intensa e recuperação de senha ficam fora do escopo; para crescimento além de poucos usuários será necessária migração para banco de dados.


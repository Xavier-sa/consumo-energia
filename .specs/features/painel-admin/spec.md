# Painel administrativo

## Objetivo

Permitir que a conta legada Xavier consulte, por meio de um botão e modal, todos os usuários e leituras cadastrados, sem conceder acesso administrativo às demais contas nem expor dados secretos.

## Decisões

- A conta legada `XAVIER`, sem papel definido, é promovida para `admin` no próximo login.
- Todo novo cadastro recebe explicitamente o papel `user`, inclusive se usar variação do nome Xavier.
- O painel é somente leitura.
- Leituras pertencem a residências; o painel apresenta os moradores associados a cada residência.
- O visual reutiliza cores, tipografia, tabelas e botões existentes.

## Critérios de aceitação

- **ADMIN-01:** QUANDO Xavier autenticar uma conta legada, ENTÃO a sessão DEVE conter `role: admin` e o JSON DEVE persistir esse papel.
- **ADMIN-02:** QUANDO qualquer conta nova for cadastrada, ENTÃO seu papel DEVE ser `user` e não pode ser elevado pelo nome escolhido.
- **ADMIN-03:** QUANDO um administrador solicitar o painel, ENTÃO a API DEVE retornar todos os usuários e leituras, associando cada leitura aos moradores da residência.
- **ADMIN-04:** QUANDO um usuário comum solicitar o painel, ENTÃO a API DEVE responder HTTP 403; sem sessão, HTTP 401.
- **ADMIN-05:** QUANDO a API responder, ENTÃO não DEVE incluir senha, hash, IP ou lista bruta de acessos.
- **ADMIN-06:** QUANDO Xavier entrar na aplicação, ENTÃO DEVE ver o botão `Painel admin`; usuários comuns não devem vê-lo.
- **ADMIN-07:** QUANDO Xavier acionar o botão, ENTÃO DEVE abrir um modal com tabela de usuários, tabela de leituras, filtro por usuário e botão de fechar.
- **ADMIN-08:** QUANDO o filtro mudar, ENTÃO a tabela de leituras DEVE mostrar apenas residências ligadas ao usuário selecionado; a opção “Todos” restaura a lista completa.

## Fora do escopo

- Alterar ou excluir usuários e leituras pelo painel.
- Criar outros administradores pela interface.
- Exibir senhas, hashes ou endereços IP.
- Paginação administrativa e recuperação de senha.


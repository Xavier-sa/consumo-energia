# Design do painel administrativo

## Backend

`RepositorioUsuarios` normaliza e persiste o papel no login e fornece uma visão sanitizada. `RepositorioLeituras` fornece a coleção completa somente ao serviço administrativo. Um controlador dedicado verifica `role === admin` antes de responder.

## Resposta administrativa

- `usuarios`: identificador, username, residencia_id, role, ultimo_acesso e total_leituras.
- `leituras`: campos de medição existentes, residencia_id e `usuarios` com os moradores da residência.

## Interface

O cabeçalho mostra um botão secundário apenas para admin. O modal usa cabeçalho fixo, cartões de resumo, tabela compacta de usuários e tabela de leituras com filtro. Em telas pequenas, as tabelas mantêm rolagem horizontal.

## Riscos

- **Escalada por nome:** novos cadastros recebem `role: user` explicitamente.
- **Vazamento:** a resposta é construída por lista permitida de campos, nunca por remoção posterior.
- **Acesso direto à rota:** autorização ocorre no backend, independentemente da visibilidade do botão.


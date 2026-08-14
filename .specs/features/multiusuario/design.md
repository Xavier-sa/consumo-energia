# Design: multiusuário por residência

## Modelo compatível

`usuarios.json` passa a aceitar `identificador`, `password_hash` e `residencia_id`. Registros com `password` continuam autenticáveis e são migrados no login. `consumo.json` passa a aceitar `residencia_id`; ausência equivale a `1`.

## Fluxo

O controlador obtém `residencia_id` exclusivamente da sessão e o repassa ao serviço de leituras. O repositório filtra e autoriza todas as operações por essa chave. O cadastro calcula novos identificadores de usuário e residência e grava somente o hash da senha.

## Riscos e mitigação

- **Acesso cruzado:** IDs nunca são autorizados sem conferir a residência.
- **Senhas legadas:** migração oportunista evita exigir redefinição e remove o texto simples após login válido.
- **Perda de dados no deploy:** não há migração destrutiva nem alteração dos JSON reais versionados.
- **Concorrência do JSON:** aceitável apenas para uso pequeno; banco relacional permanece evolução recomendada.


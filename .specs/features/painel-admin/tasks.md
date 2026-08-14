# Tarefas do painel administrativo

## Testes e gates

- Backend e HTTP: `php tests/executar.php` e `bash tests/http.sh`.
- Interface: `node --experimental-default-type=module tests/interface.mjs`.
- Gate completo: `bash tools/ci/verificar-padrao.sh && bash tools/ci/verificar-seguranca.sh`.

## Plano

1. **T1 — Especificação:** registrar autorização, privacidade e comportamento visual.
2. **T2 — API administrativa:** papel, serviço, controlador, rota e testes de 401/403/200 e sanitização. Depende de T1.
3. **T3 — Modal administrativo:** botão condicional, modal, tabelas, filtro, CSS e teste comportamental. Depende de T2.
4. **T4 — Validação:** gate completo e verificação independente. Depende de T3.

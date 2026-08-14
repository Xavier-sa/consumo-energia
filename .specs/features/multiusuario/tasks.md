# Tarefas: multiusuário por residência

## Matriz de testes

| Camada | Tipo | Cobertura | Comando |
|---|---|---|---|
| Autenticação e repositórios | integração PHP | Cadastro, duplicidade, login legado, hash e isolamento | `php tests/executar.php` |
| Interface | validação estática | Sintaxe dos módulos e presença dos controles | `bash tools/ci/verificar-padrao.sh` |
| Segurança | inspeção automatizada | Dados reais, segredos e credenciais de exemplo | `bash tools/ci/verificar-seguranca.sh` |

## Execução

1. **T1 — Especificação:** registrar requisitos, decisões e design. Sem dependências.
2. **T2 — Backend e testes:** implementar cadastro, migração de senha e isolamento por residência; criar testes. Depende de T1.
3. **T3 — Interface:** criar alternância entre login e cadastro e integrar a API. Depende de T2.
4. **T4 — Publicação:** documentar URL pública, sanitizar exemplo e incluir testes na CI. Depende de T3.

Cada tarefa exige seus testes aplicáveis e as verificações de padrão e segurança aprovadas antes do commit.

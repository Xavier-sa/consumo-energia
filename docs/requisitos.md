# Levantamento de requisitos

## 1. Identificação

- **Sistema:** Meu Consumo.
- **Versão do documento:** 1.0.
- **Situação:** correspondente à implementação atual.
- **Objetivo:** controlar leituras residenciais de energia durante o dia.

## 2. Visão do produto

O Meu Consumo permite que uma pessoa autenticada registre a leitura do medidor
pela manhã e à noite. O sistema calcula a diferença entre os valores, armazena
o consumo diário e mantém um histórico consultável.

Cada leitura pode receber uma foto opcional do medidor em cada período. O
sistema registra o horário associado à foto para facilitar a conferência.

## 3. Escopo

### 3.1 Incluído

- Autenticação por usuário e senha.
- Controle de sessão.
- Registro, consulta, edição e exclusão de leituras.
- Cálculo automático do consumo diário.
- Upload e visualização de fotos do medidor.
- Paginação do histórico.
- Exportação das leituras em CSV.
- Persistência em arquivos JSON.
- Interface responsiva em português.

### 3.2 Fora do escopo atual

- Cadastro de unidades consumidoras ou medidores.
- Separação das leituras por usuário.
- Recuperação de senha.
- Cadastro de usuários pela interface.
- Cálculo de valor monetário da conta.
- Integração com a Energisa ou leitura automática do medidor.
- Relatórios mensais e gráficos comparativos.
- Notificações ou lembretes.
- Banco de dados relacional.

## 4. Atores

| Ator | Descrição |
| --- | --- |
| Usuário | Pessoa que registra e consulta as leituras de energia. |
| Administrador da hospedagem | Configura credenciais, permissões e publicação. |
| Sistema de arquivos | Mantém JSON, sessões e fotos no servidor. |

## 5. Requisitos funcionais

| ID | Requisito | Prioridade | Situação |
| --- | --- | --- | --- |
| RF-001 | O sistema deve autenticar o usuário por nome e senha. | Alta | Implementado |
| RF-002 | O sistema deve manter a autenticação durante a sessão PHP. | Alta | Implementado |
| RF-003 | O sistema deve permitir encerrar a sessão. | Alta | Implementado |
| RF-004 | O sistema deve registrar data, leitura da manhã e leitura da noite. | Alta | Implementado |
| RF-005 | O sistema deve calcular o consumo pela diferença entre noite e manhã. | Alta | Implementado |
| RF-006 | O sistema deve permitir uma foto opcional para cada período. | Média | Implementado |
| RF-007 | O sistema deve registrar o horário de cada foto enviada. | Média | Implementado |
| RF-008 | O sistema deve listar as leituras da mais recente para a mais antiga. | Alta | Implementado |
| RF-009 | O sistema deve paginar o histórico de leituras. | Média | Implementado |
| RF-010 | O sistema deve permitir editar uma leitura existente. | Alta | Implementado |
| RF-011 | O sistema deve permitir excluir uma leitura após confirmação. | Alta | Implementado |
| RF-012 | O sistema deve excluir as fotos associadas ao excluir a leitura. | Alta | Implementado |
| RF-013 | O sistema deve substituir a foto anterior ao enviar uma nova na edição. | Média | Implementado |
| RF-014 | O sistema deve exportar o histórico em formato CSV. | Média | Implementado |
| RF-015 | O sistema deve criar os JSON reais a partir dos modelos na primeira execução. | Alta | Implementado |

## 6. Requisitos não funcionais

| ID | Requisito | Categoria |
| --- | --- | --- |
| RNF-001 | A aplicação deve executar em PHP 7.4 ou superior. | Compatibilidade |
| RNF-002 | A interface deve funcionar em celular e computador. | Usabilidade |
| RNF-003 | Todos os textos apresentados ao usuário devem estar em português. | Usabilidade |
| RNF-004 | A API deve exigir sessão para operações de leitura e escrita. | Segurança |
| RNF-005 | O upload deve aceitar somente JPEG, PNG ou WebP de até 5 MB. | Segurança |
| RNF-006 | O sistema deve verificar o tipo real da imagem com `fileinfo`. | Segurança |
| RNF-007 | Os arquivos enviados devem receber nomes aleatórios. | Segurança |
| RNF-008 | Os JSON reais e as fotos não devem ser versionados no Git. | Privacidade |
| RNF-009 | A persistência deve usar escrita temporária e substituição atômica. | Integridade |
| RNF-010 | A interface deve apresentar foco visível e marcação semântica básica. | Acessibilidade |
| RNF-011 | A aplicação não deve exigir Composer, Node.js ou banco externo. | Implantação |

## 7. Regras de negócio

| ID | Regra |
| --- | --- |
| RN-001 | A data deve existir e usar o formato `AAAA-MM-DD`. |
| RN-002 | As leituras devem ser números inteiros iguais ou maiores que zero. |
| RN-003 | A leitura da noite deve ser igual ou maior que a leitura da manhã. |
| RN-004 | O consumo diário é `leitura_noite - leitura_manha`. |
| RN-005 | Uma foto é opcional e pertence a apenas um período da leitura. |
| RN-006 | O horário da foto é armazenado somente quando a foto existe. |
| RN-007 | A substituição de foto remove o arquivo anterior do servidor. |
| RN-008 | A exclusão da leitura remove os arquivos de foto associados. |
| RN-009 | Somente uma sessão autenticada pode manipular ou exportar leituras. |
| RN-010 | O identificador de leitura é o maior identificador existente mais um. |

## 8. Casos de uso resumidos

### UC-001 — Entrar no sistema

1. O usuário informa nome e senha.
2. O sistema compara as credenciais com `usuarios.json`.
3. O sistema registra data, hora e IP do acesso válido.
4. O sistema inicia a sessão e apresenta o controle de consumo.

**Exceção:** se as credenciais forem inválidas, o sistema mantém a tela de
entrada e apresenta uma mensagem de erro.

### UC-002 — Registrar leitura

1. O usuário informa a data e as duas leituras.
2. Opcionalmente, o usuário adiciona uma foto para cada período.
3. O sistema valida os dados e as imagens.
4. O sistema calcula o consumo.
5. O sistema armazena a leitura e atualiza o histórico.

### UC-003 — Editar leitura

1. O usuário seleciona **Editar** no histórico.
2. O sistema preenche o formulário com os valores existentes.
3. O usuário altera valores ou envia novas fotos.
4. O sistema valida e substitui os dados modificados.

### UC-004 — Excluir leitura

1. O usuário seleciona **Excluir**.
2. O sistema solicita confirmação.
3. O sistema remove a leitura e suas fotos.
4. O sistema atualiza o histórico.

### UC-005 — Exportar histórico

1. O usuário seleciona **Exportar CSV**.
2. O sistema gera um CSV com todas as leituras.
3. O navegador inicia o download.

## 9. Critérios de aceite essenciais

### CA-001 — Cálculo válido

- **Dado** que a leitura da manhã é `515` e a da noite é `518`.
- **Quando** o usuário salva o registro.
- **Então** o consumo armazenado deve ser `3 kWh`.

### CA-002 — Ordem inválida

- **Dado** que a leitura da manhã é `520` e a da noite é `518`.
- **Quando** o usuário tenta salvar.
- **Então** o sistema deve rejeitar a operação e explicar a regra.

### CA-003 — Upload válido

- **Dado** um arquivo JPEG, PNG ou WebP com até 5 MB.
- **Quando** o usuário salva a leitura.
- **Então** o sistema deve armazenar a foto com nome aleatório e seu horário.

### CA-004 — Upload inválido

- **Dado** um arquivo de formato não aceito ou maior que 5 MB.
- **Quando** o usuário tenta salvar a leitura.
- **Então** o sistema deve rejeitar o arquivo sem persistir uma leitura parcial.

### CA-005 — Proteção de sessão

- **Dado** um visitante sem sessão válida.
- **Quando** ele solicita uma operação protegida da API.
- **Então** a API deve responder com HTTP `401`.

## 10. Premissas e restrições

- A aplicação atende uma instalação pequena e de baixo volume.
- Os JSON pertencem exclusivamente a esta aplicação.
- O servidor executa uma única instância ou mantém o armazenamento compartilhado.
- O administrador realiza cópias de segurança de `data/` e `public/uploads/`.
- O modelo atual não associa uma leitura ao usuário que a criou.
- As senhas atuais usam texto simples e precisam de hash antes de uso público.

## 11. Melhorias recomendadas

1. Armazenar senhas com `password_hash()` e validar com `password_verify()`.
2. Adicionar `usuario_id` às leituras para definir autoria e isolamento.
3. Impedir ou atualizar registros duplicados para a mesma data e medidor.
4. Registrar data de criação, atualização e autoria das alterações.
5. Adicionar testes automatizados de domínio, API e interface.
6. Migrar para SQLite ou outro banco quando houver concorrência ou mais volume.

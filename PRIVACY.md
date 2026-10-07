# Política de Privacidade

**Versão:** 1.0
**Vigência:** 7 de outubro de 2026

Esta Política explica como o Meu Consumo trata dados pessoais. Ela descreve o
comportamento atual do código; a adequação completa à Lei Geral de Proteção de
Dados Pessoais (LGPD) também depende dos processos adotados por quem instala e
opera a aplicação.

## Dados tratados e finalidades

A aplicação trata:

- **nome de usuário:** identifica a conta e permite autenticação;
- **senha:** autentica o acesso; novas senhas são armazenadas como hash, e não
  em formato legível;
- **identificadores da conta, residência e perfil:** separam históricos e
  controlam permissões;
- **endereço IP e datas de acesso:** são registrados no cadastro e nos logins
  para histórico de acesso e apoio à segurança;
- **data, turno, valores e consumo das leituras:** formam o histórico de
  acompanhamento de energia;
- **fotos opcionais do medidor:** documentam a leitura quando o usuário decide
  enviá-las;
- **data e versões dos documentos aceitos:** demonstram quais Termos de Uso e
  Política de Privacidade foram aceitos na criação da conta.

Não use o nome de usuário ou as fotos para fornecer informações pessoais além
do necessário.

## Bases legais

Os dados necessários para criar a conta, autenticar, separar residências e
manter o histórico são tratados para fornecer as funcionalidades solicitadas
pelo usuário, em contexto compatível com execução de contrato ou de
procedimentos preliminares, conforme aplicável.

Registros de segurança e acesso podem apoiar o legítimo interesse de proteger
a aplicação e apurar incidentes, mediante avaliação pelo responsável pela
instalação. Obrigações legais poderão justificar retenções específicas quando
aplicáveis.

O aceite destes documentos confirma ciência e concordância contratual, mas não
é apresentado como consentimento genérico para todo tratamento. Se uma
instalação adicionar uma finalidade opcional baseada em consentimento, deverá
solicitá-lo separadamente e permitir sua revogação.

## Armazenamento e conservação

Contas e registros de acesso ficam em `data/usuarios.json`. Leituras e
referências às fotos ficam em `data/consumo.json`; as imagens ficam em
`public/uploads/`. Esses arquivos permanecem no servidor da instalação.

O código não define prazo automático de exclusão. Os dados permanecem até uma
ação administrativa de correção ou exclusão. Antes do uso em produção, o
responsável pela instalação deve definir prazos proporcionais a cada finalidade
e procedimentos seguros para cópias de segurança.

## Compartilhamento

O código não contém integração destinada a compartilhar dados pessoais com
terceiros. O provedor de hospedagem poderá processar dados para manter a
infraestrutura, conforme a configuração de cada instalação. O responsável pela
instalação deve documentar outros operadores ou compartilhamentos que adotar.

Usuários com perfil administrativo podem consultar nomes de usuário, vínculos
com residências, perfis, total de registros e datas de último acesso, além das
leituras. A interface administrativa não retorna senhas, hashes ou endereços
IP.

## Segurança

Novas senhas usam hash do mecanismo seguro da plataforma PHP. O projeto separa
dados por residência, protege rotas com sessão, restringe os tipos de imagem e
inclui regras para impedir acesso direto aos JSONs e execução de scripts nos
uploads. O operador deve usar HTTPS, limitar permissões, atualizar o ambiente e
manter cópias de segurança protegidas.

Nenhuma medida elimina todos os riscos. Existem contas históricas que podem
usar o formato legado de senha até o próximo login, quando o código converte a
senha para hash. O responsável pela instalação deve tratar essa pendência como
risco crítico, sem expor ou copiar as credenciais.

## Direitos dos titulares

Nos limites da LGPD, você pode solicitar:

- confirmação da existência de tratamento e acesso aos dados;
- correção de dados incompletos, inexatos ou desatualizados;
- anonimização, bloqueio ou eliminação de dados desnecessários, excessivos ou
  tratados em desconformidade;
- informação sobre compartilhamentos;
- informação sobre a possibilidade de negar consentimento e suas consequências,
  quando essa for a base legal;
- revogação do consentimento, quando aplicável;
- eliminação dos dados tratados com consentimento, observadas as hipóteses
  legais de conservação.

A eliminação não é absoluta quando uma obrigação legal ou outra base legítima
exigir conservação. A interface ainda não oferece autoatendimento para acesso,
correção ou exclusão da conta.

## Solicitações sobre dados

O repositório não informa um canal de atendimento ao titular nem identifica o
controlador de cada instalação. Antes do uso em produção, o responsável pela
instalação deve publicar sua identidade, um canal seguro para solicitações e o
procedimento de verificação do solicitante. Não envie senhas, fotos de medidor
ou outros dados sensíveis em issues públicas do projeto.

## Atualizações desta Política

Esta Política pode mudar para acompanhar o funcionamento da aplicação ou
requisitos legais. Cada versão deve informar sua vigência. O sistema não cria
aceites retroativos para contas antigas.

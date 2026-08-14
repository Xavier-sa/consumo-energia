Este diretório contém os arquivos JSON usados como banco de dados para a aplicação.
Não exponha diretamente via servidor estático; o servidor fornece endpoints controlados.

Nota de uso (em português):
- Cada entrada registra `leitura_manha` e `leitura_noite`. O campo `consumo` é a diferença entre a leitura da noite e a leitura da manhã, não a soma.
- Os usuários são armazenados em `usuarios.json`.
- As leituras são armazenadas em `consumo.json`.

Os arquivos reais `usuarios.json` e `consumo.json` são locais e estão no `.gitignore`, pois podem conter credenciais, endereços IP e dados de consumo. Os arquivos versionáveis são:

- `usuarios.exemplo.json`: modelo sanitizado para os usuários.
- `consumo.exemplo.json`: modelo vazio para as leituras.

Na primeira execução, o `bootstrap.php` cria automaticamente os arquivos reais copiando esses modelos, caso ainda não existam. Antes de publicar, altere o usuário e a senha criados em `usuarios.json`.

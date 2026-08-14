#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/../.."

echo "Verificando sintaxe PHP..."
while IFS= read -r -d '' arquivo; do
  php -l "$arquivo" >/dev/null
done < <(find app public views -type f -name '*.php' -print0)
php -l bootstrap.php >/dev/null
php -l index.php >/dev/null

echo "Verificando módulos JavaScript..."
while IFS= read -r -d '' arquivo; do
  node --experimental-default-type=module --check "$arquivo"
done < <(find public/assets/js -type f -name '*.js' -print0)

echo "Verificando JSONs versionáveis..."
php -r '
foreach (glob("data/*.exemplo.json") as $arquivo) {
    json_decode(file_get_contents($arquivo), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        fwrite(STDERR, "$arquivo: " . json_last_error_msg() . PHP_EOL);
        exit(1);
    }
}
'

echo "Verificando marcadores de conflito..."
if git grep -nE '^(<<<<<<<|=======|>>>>>>>)' -- ':!docs/diagramas/*.svg'; then
  echo "Erro: existem marcadores de conflito no repositório." >&2
  exit 1
fi

echo "Verificando espaços no fim das linhas..."
if git grep -nE '[[:blank:]]+$' -- '*.php' '*.js' '*.css' '*.json' '*.md' '*.yml' '*.yaml' '*.sh'; then
  echo "Erro: remova os espaços no fim das linhas." >&2
  exit 1
fi

echo "Verificando arquivos antigos da estrutura..."
for arquivo in public/index.html public/app.js public/styles.css server.js package.json; do
  if [[ -e "$arquivo" ]]; then
    echo "Erro: arquivo obsoleto encontrado: $arquivo" >&2
    exit 1
  fi
done

echo "Verificações de padrão concluídas."

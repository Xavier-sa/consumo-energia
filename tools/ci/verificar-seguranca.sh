#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/../.."

falhar() {
  echo "Erro de segurança: $1" >&2
  exit 1
}

echo "Verificando dados reais..."
for arquivo in data/usuarios.json data/consumo.json; do
  if git ls-files --error-unmatch "$arquivo" >/dev/null 2>&1; then
    falhar "$arquivo contém dados locais e não pode ser versionado."
  fi
done

echo "Verificando conteúdo da pasta de uploads..."
while IFS= read -r arquivo; do
  case "$arquivo" in
    public/uploads/.htaccess|public/uploads/.gitkeep) ;;
    *) falhar "arquivo indevido versionado em uploads: $arquivo" ;;
  esac
done < <(git ls-files 'public/uploads/*')

grep -q 'Require all denied' public/uploads/.htaccess \
  || falhar "o .htaccess de uploads não bloqueia scripts."

echo "Procurando segredos conhecidos..."
segredos='AKIA[0-9A-Z]{16}|ASIA[0-9A-Z]{16}|gh[pousr]_[A-Za-z0-9_]{20,}|sk_live_[A-Za-z0-9]{16,}|-----BEGIN ([A-Z ]+)?PRIVATE KEY-----'
if git grep -nEI "$segredos" -- ':!docs/diagramas/*.svg'; then
  falhar "possível chave, token ou chave privada encontrada."
fi

echo "Verificando APIs JavaScript perigosas..."
padroes_js='\.innerHTML|\.outerHTML|insertAdjacentHTML|document\.write|document\.writeln|eval\(|new Function|setAttribute\(["'\'']on|javascript:'
if git grep -nE "$padroes_js" -- 'public/assets/js'; then
  falhar "foi encontrada uma API JavaScript proibida."
fi

echo "Verificando scripts inline nas views..."
if git grep -nEi '<script[^>]*>[^<]+' -- 'views' 'public'; then
  falhar "scripts inline não são permitidos."
fi
if git grep -nEi '\son[a-z]+=' -- 'views' 'public'; then
  falhar "eventos HTML inline não são permitidos."
fi

echo "Verificando credencial do modelo..."
php -r '
$usuarios = json_decode(file_get_contents("data/usuarios.exemplo.json"), true);
if (!is_array($usuarios) || count($usuarios) !== 1) exit(1);
if (($usuarios[0]["password"] ?? "") !== "ALTERE_ESTA_SENHA") exit(1);
if (($usuarios[0]["accesses"] ?? null) !== []) exit(1);
if (!array_key_exists("last_ip", $usuarios[0]) || $usuarios[0]["last_ip"] !== null) exit(1);
' || falhar "usuarios.exemplo.json deve permanecer sanitizado."

echo "Verificações de segurança concluídas."

#!/usr/bin/env bash
set -euo pipefail

raiz_projeto=$(cd "$(dirname "$0")/.." && pwd)
diretorio_teste=$(mktemp -d)
porta=18765
servidor_pid=''

encerrar() {
  if [[ -n "$servidor_pid" ]]; then kill "$servidor_pid" 2>/dev/null || true; fi
  rm -rf "$diretorio_teste"
}
trap encerrar EXIT

cp -R "$raiz_projeto/app" "$raiz_projeto/public" "$raiz_projeto/views" "$raiz_projeto/data" "$raiz_projeto/bootstrap.php" "$diretorio_teste/"
php -r '
file_put_contents($argv[1] . "/data/usuarios.json", json_encode([
    ["username" => "LEGADO", "password" => "senha-legada", "accesses" => []],
    ["username" => "XAVIER", "password" => "senha-xavier", "accesses" => []]
]));
file_put_contents($argv[1] . "/data/consumo.json", json_encode([
    ["identificador" => 1, "data" => "2026-08-13", "leitura_manha" => 515, "leitura_noite" => 518, "consumo" => 3],
    ["identificador" => 2, "data" => "2026-08-14", "leitura_manha" => 521, "leitura_noite" => null, "consumo" => null]
]));
' "$diretorio_teste"

php -S "127.0.0.1:$porta" -t "$diretorio_teste/public" >"$diretorio_teste/servidor.log" 2>&1 &
servidor_pid=$!
for _ in 1 2 3 4 5; do
  if curl -fsS "http://127.0.0.1:$porta/" >/dev/null 2>&1; then break; fi
  sleep 0.1
done

requisitar() {
  local cookie=$1
  local acao=$2
  local metodo=$3
  local corpo=${4:-}
  local saida=$5
  curl -sS -b "$cookie" -c "$cookie" -X "$metodo" -H 'Content-Type: application/json' \
    ${corpo:+--data "$corpo"} -o "$saida" -w '%{http_code}' "http://127.0.0.1:$porta/api.php?action=$acao"
}

cookie_alice="$diretorio_teste/alice.cookie"
cookie_bob="$diretorio_teste/bob.cookie"
cookie_legado="$diretorio_teste/legado.cookie"
cookie_xavier="$diretorio_teste/xavier.cookie"
resposta="$diretorio_teste/resposta"

[[ $(requisitar "$cookie_alice" entries GET '' "$resposta") == '401' ]]
[[ $(requisitar "$cookie_alice" admin_dashboard GET '' "$resposta") == '401' ]]
[[ $(requisitar "$cookie_alice" register POST '{"username":"ALICE","password":"senha-forte","password_confirmation":"diferente"}' "$resposta") == '422' ]]
[[ $(requisitar "$cookie_alice" register POST '{"username":"ALICE","password":"senha-forte","password_confirmation":"senha-forte"}' "$resposta") == '201' ]]
grep -q '"username":"ALICE"' "$resposta"
grep -q '"residencia_id":2' "$resposta"
[[ $(requisitar "$cookie_alice" me GET '' "$resposta") == '200' ]]
grep -q '"username":"ALICE"' "$resposta"
[[ $(requisitar "$cookie_alice" entries GET '' "$resposta") == '200' ]]
grep -q '"total":0' "$resposta"

[[ $(requisitar "$cookie_alice" create_entry POST '{"date":"2026-08-14","shift":"morning","morning":"100"}' "$resposta") == '200' ]]
grep -q '"residencia_id":2' "$resposta"
id_alice=$(php -r '$d=json_decode(file_get_contents($argv[1]), true); echo $d["item"]["identificador"];' "$resposta")

[[ $(requisitar "$cookie_bob" register POST '{"username":"BOB","password":"senha-forte","password_confirmation":"senha-forte"}' "$resposta") == '201' ]]
[[ $(requisitar "$diretorio_teste/duplicado.cookie" register POST '{"username":"bob","password":"senha-forte","password_confirmation":"senha-forte"}' "$resposta") == '422' ]]
[[ $(requisitar "$cookie_bob" entries GET '' "$resposta") == '200' ]]
grep -q '"total":0' "$resposta"
[[ $(requisitar "$cookie_bob" "update_entry&id=$id_alice" POST '{"date":"2026-08-14","shift":"morning","morning":"999"}' "$resposta") == '404' ]]
[[ $(requisitar "$cookie_bob" "delete_entry&id=$id_alice" POST '{}' "$resposta") == '404' ]]
[[ $(requisitar "$cookie_bob" export GET '' "$resposta") == '200' ]]
if grep -q '2026-08-14' "$resposta"; then exit 1; fi
[[ $(requisitar "$cookie_alice" "update_entry&id=$id_alice" POST '{"date":"2026-08-14","shift":"morning","morning":"101"}' "$resposta") == '200' ]]
[[ $(requisitar "$cookie_alice" "delete_entry&id=$id_alice" POST '{}' "$resposta") == '200' ]]

[[ $(requisitar "$cookie_legado" login POST '{"username":"LEGADO","password":"incorreta"}' "$resposta") == '401' ]]
grep -q '"message":"Usuário ou senha inválidos."' "$resposta"
[[ $(requisitar "$cookie_legado" login POST '{"username":"LEGADO","password":"senha-legada"}' "$resposta") == '200' ]]
[[ $(requisitar "$cookie_legado" entries GET '' "$resposta") == '200' ]]
grep -q '"total":2' "$resposta"
grep -q '"leitura_noite":null' "$resposta"
[[ $(requisitar "$cookie_legado" admin_dashboard GET '' "$resposta") == '403' ]]

[[ $(requisitar "$cookie_xavier" login POST '{"username":"XAVIER","password":"senha-xavier"}' "$resposta") == '200' ]]
grep -q '"role":"admin"' "$resposta"
[[ $(requisitar "$cookie_xavier" admin_dashboard GET '' "$resposta") == '200' ]]
grep -q '"username":"XAVIER"' "$resposta"
grep -q '"leitura_noite":null' "$resposta"
if grep -Eq 'password|last_ip|accesses|127\.0\.0' "$resposta"; then exit 1; fi

echo '✓ rotas HTTP protegem sessão, cadastro e isolamento por residência'

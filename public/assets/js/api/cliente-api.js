async function requisitar(url, opcoes = {}) {
  const configuracao = { ...opcoes };
  if (configuracao.body && !(configuracao.body instanceof FormData)) {
    configuracao.headers = { 'Content-Type': 'application/json' };
    configuracao.body = JSON.stringify(configuracao.body);
  }
  const resposta = await fetch(url, configuracao);
  const dados = await resposta.json().catch(() => ({}));
  if (!resposta.ok) throw new Error(dados.message || 'Não foi possível concluir a operação.');
  return dados;
}

export const api = {
  entrar: (usuario, senha) => requisitar('api.php?action=login', { method: 'POST', body: { username: usuario, password: senha } }),
  cadastrar: (usuario, senha, confirmacao, aceiteDocumentos) => requisitar('api.php?action=register', {
    method: 'POST',
    body: { username: usuario, password: senha, password_confirmation: confirmacao, aceite_documentos: aceiteDocumentos },
  }),
  usuarioAtual: () => requisitar('api.php?action=me'),
  listarLeituras: (pagina = 1, limite = 8) => requisitar(`api.php?action=entries&page=${pagina}&limit=${limite}`),
  criarLeitura: (dados) => requisitar('api.php?action=create_entry', { method: 'POST', body: dados }),
  atualizarLeitura: (id, dados) => requisitar(`api.php?action=update_entry&id=${id}`, { method: 'POST', body: dados }),
  excluirLeitura: (id) => requisitar(`api.php?action=delete_entry&id=${id}`, { method: 'POST' }),
  painelAdmin: () => requisitar('api.php?action=admin_dashboard'),
  sair: () => requisitar('api.php?action=logout', { method: 'POST' }),
  urlExportacao: 'api.php?action=export',
};

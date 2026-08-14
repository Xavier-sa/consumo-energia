import { api } from './api/cliente-api.js?v=20260814-2';
import { criarControleAcesso } from './componentes/acesso.js?v=20260814-3';
import { criarFormularioLeitura } from './componentes/formulario-leitura.js?v=20260814-2';
import { criarHistorico } from './componentes/historico.js?v=20260814-2';
import { dataExtensoHoje, formatarData } from './utilitarios/datas.js';

const limite = 8;
let paginaAtual = 1;
let formulario;
let historico;

function mostrarLogin() {
  document.querySelector('#login-view').classList.remove('hidden');
  document.querySelector('#main-view').classList.add('hidden');
}

async function carregarLeituras(pagina = 1) {
  paginaAtual = pagina;
  try {
    historico.renderizar(await api.listarLeituras(pagina, limite), pagina);
  } catch (erro) {
    if (erro.message.includes('sessão')) mostrarLogin();
  }
}

function mostrarAplicacao(usuario) {
  document.querySelector('#user-name').textContent = usuario;
  document.querySelector('#login-view').classList.add('hidden');
  document.querySelector('#main-view').classList.remove('hidden');
  carregarLeituras();
}

async function iniciar() {
  formulario = criarFormularioLeitura(async (id, dados) => {
    id ? await api.atualizarLeitura(id, dados) : await api.criarLeitura(dados);
    await carregarLeituras(id ? paginaAtual : 1);
  });
  historico = criarHistorico({
    limite,
    aoEditar: formulario.editar,
    aoMudarPagina: carregarLeituras,
    aoExcluir: async (leitura) => {
      if (!confirm(`Excluir a leitura de ${formatarData(leitura.data)}?`)) return;
      try { await api.excluirLeitura(leitura.identificador); await carregarLeituras(paginaAtual); }
      catch (erro) { alert(erro.message); }
    },
  });

  document.querySelector('#today-label').textContent = dataExtensoHoje();
  criarControleAcesso();
  document.querySelector('#login-form').addEventListener('submit', async (evento) => {
    evento.preventDefault();
    try {
      const resposta = await api.entrar(document.querySelector('#username').value, document.querySelector('#password').value);
      document.querySelector('#login-error').textContent = '';
      mostrarAplicacao(resposta.user.username);
    } catch (erro) { document.querySelector('#login-error').textContent = erro.message; }
  });
  document.querySelector('#register-form').addEventListener('submit', async (evento) => {
    evento.preventDefault();
    const usuario = document.querySelector('#register-username').value;
    const senha = document.querySelector('#register-password').value;
    const confirmacao = document.querySelector('#register-confirmation').value;
    try {
      const resposta = await api.cadastrar(usuario, senha, confirmacao);
      document.querySelector('#register-error').textContent = '';
      mostrarAplicacao(resposta.user.username);
    } catch (erro) { document.querySelector('#register-error').textContent = erro.message; }
  });
  document.querySelector('#btn-logout').addEventListener('click', async () => { await api.sair(); location.reload(); });
  document.querySelector('#btn-export').addEventListener('click', () => {
    const link = document.createElement('a'); link.href = api.urlExportacao; link.download = 'historico-de-consumo.csv'; link.click();
  });

  try {
    const resposta = await api.usuarioAtual();
    resposta.user ? mostrarAplicacao(resposta.user.username) : mostrarLogin();
  } catch { mostrarLogin(); }
}

iniciar();

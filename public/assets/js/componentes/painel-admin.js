import { formatarData, formatarHora } from '../utilitarios/datas.js';

export const podeAdministrar = (usuario) => usuario?.role === 'admin';

export function filtrarLeituras(leituras, residenciaId) {
  if (residenciaId === '') return leituras;
  return leituras.filter((leitura) => String(leitura.residencia_id) === String(residenciaId));
}

export function criarPainelAdmin(api, documento = document) {
  const botao = documento.querySelector('#btn-admin');
  const dialogo = documento.querySelector('#admin-dialog');
  const filtro = documento.querySelector('#admin-user-filter');
  let dados = { usuarios: [], leituras: [] };

  const celula = (texto, classe = '') => {
    const item = documento.createElement('td');
    item.textContent = texto;
    if (classe) item.className = classe;
    return item;
  };

  function renderizarLeituras() {
    const linhas = filtrarLeituras(dados.leituras, filtro.value).map((leitura) => {
      const linha = documento.createElement('tr');
      const valor = (numero) => numero === null || numero === undefined ? '—' : `${numero} kWh`;
      linha.append(
        celula(leitura.usuarios.join(', ') || 'Sem usuário'), celula(formatarData(leitura.data)),
        celula(valor(leitura.leitura_manha)), celula(valor(leitura.leitura_noite)), celula(valor(leitura.consumo))
      );
      return linha;
    });
    documento.querySelector('#admin-readings-table tbody').replaceChildren(...linhas);
  }

  function renderizar() {
    const linhas = dados.usuarios.map((usuario) => {
      const linha = documento.createElement('tr');
      linha.append(
        celula(usuario.username), celula(`#${usuario.residencia_id}`),
        celula(usuario.role === 'admin' ? 'Administrador' : 'Usuário', 'admin-role'),
        celula(String(usuario.total_leituras)), celula(usuario.ultimo_acesso ? formatarHora(usuario.ultimo_acesso) : 'Nunca')
      );
      return linha;
    });
    documento.querySelector('#admin-users-table tbody').replaceChildren(...linhas);
    const opcoes = dados.usuarios.map((usuario) => {
      const opcao = documento.createElement('option');
      opcao.value = String(usuario.residencia_id);
      opcao.textContent = usuario.username;
      return opcao;
    });
    filtro.replaceChildren(documento.createElement('option'), ...opcoes);
    filtro.firstChild.value = '';
    filtro.firstChild.textContent = 'Todos';
    renderizarLeituras();
  }

  async function abrir() {
    dialogo.showModal();
    documento.querySelector('#admin-message').textContent = 'Carregando dados...';
    try {
      dados = await api.painelAdmin();
      documento.querySelector('#admin-message').textContent = '';
      renderizar();
    } catch (erro) {
      documento.querySelector('#admin-message').textContent = erro.message;
    }
  }

  botao.addEventListener('click', abrir);
  documento.querySelector('#btn-close-admin').addEventListener('click', () => dialogo.close());
  dialogo.addEventListener('click', (evento) => { if (evento.target === dialogo) dialogo.close(); });
  filtro.addEventListener('change', renderizarLeituras);

  return {
    configurarUsuario(usuario) { botao.classList.toggle('hidden', !podeAdministrar(usuario)); },
    abrir,
  };
}

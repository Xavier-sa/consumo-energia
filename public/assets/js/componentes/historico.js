import { dataLocalISO, formatarData, formatarHora } from '../utilitarios/datas.js';

function criarCelula(texto) {
  const celula = document.createElement('td');
  celula.textContent = texto;
  return celula;
}

function criarFotos(leitura) {
  const celula = document.createElement('td');
  celula.className = 'photo-links';
  const fotos = [
    { url: leitura.foto_manha, hora: leitura.horario_foto_manha, rotulo: 'Manhã', icone: '☀️' },
    { url: leitura.foto_noite, hora: leitura.horario_foto_noite, rotulo: 'Noite', icone: '🌙' },
  ].filter((foto) => foto.url);

  if (!fotos.length) {
    const aviso = document.createElement('span');
    aviso.className = 'no-photo';
    aviso.textContent = 'Sem foto';
    celula.appendChild(aviso);
    return celula;
  }
  fotos.forEach((foto) => {
    const link = document.createElement('a');
    link.className = 'photo-link';
    link.href = foto.url;
    link.target = '_blank';
    link.rel = 'noopener';
    link.setAttribute('aria-label', `Abrir foto da ${foto.rotulo.toLowerCase()}${foto.hora ? `, registrada às ${formatarHora(foto.hora)}` : ''}`);
    const icone = document.createElement('span');
    icone.textContent = foto.icone;
    const descricao = document.createElement('span');
    descricao.textContent = foto.rotulo;
    if (foto.hora) {
      const horario = document.createElement('small');
      horario.textContent = formatarHora(foto.hora);
      descricao.appendChild(horario);
    }
    link.append(icone, descricao);
    celula.appendChild(link);
  });
  return celula;
}

function criarLinha(leitura, aoEditar, aoExcluir) {
  const linha = document.createElement('tr');
  if (leitura.data < dataLocalISO()) linha.classList.add('past');
  const consumo = document.createElement('td');
  const consumoDestaque = document.createElement('strong');
  consumoDestaque.textContent = `${leitura.consumo} kWh`;
  consumo.appendChild(consumoDestaque);
  const acoes = document.createElement('td');
  acoes.className = 'actions';
  const editar = document.createElement('button');
  editar.className = 'action-button edit';
  editar.type = 'button';
  editar.textContent = 'Editar';
  editar.addEventListener('click', () => aoEditar(leitura));
  const excluir = document.createElement('button');
  excluir.className = 'action-button delete';
  excluir.type = 'button';
  excluir.textContent = 'Excluir';
  excluir.addEventListener('click', () => aoExcluir(leitura));
  acoes.append(editar, excluir);
  linha.append(criarCelula(formatarData(leitura.data)), criarCelula(`${leitura.leitura_manha} kWh`), criarCelula(`${leitura.leitura_noite} kWh`), consumo, criarFotos(leitura), acoes);
  return linha;
}

export function criarHistorico({ aoEditar, aoExcluir, aoMudarPagina, limite }) {
  return {
    renderizar(resultado, paginaAtual) {
      document.querySelector('#entries-table tbody').replaceChildren(...resultado.data.map((leitura) => criarLinha(leitura, aoEditar, aoExcluir)));
      document.querySelector('#empty-state').classList.toggle('hidden', resultado.total !== 0);
      document.querySelector('#table-wrap').classList.toggle('hidden', resultado.total === 0);
      document.querySelector('#btn-export').classList.toggle('hidden', resultado.total === 0);

      const paginas = Math.ceil(resultado.total / limite);
      const paginacao = document.querySelector('#pagination');
      paginacao.replaceChildren();
      paginacao.classList.toggle('hidden', paginas <= 1);
      for (let pagina = 1; pagina <= paginas; pagina += 1) {
        const botao = document.createElement('button');
        botao.type = 'button';
        botao.textContent = pagina;
        botao.setAttribute('aria-label', `Ir para a página ${pagina}`);
        if (pagina === paginaAtual) botao.setAttribute('aria-current', 'page');
        botao.addEventListener('click', () => aoMudarPagina(pagina));
        paginacao.appendChild(botao);
      }
    },
  };
}

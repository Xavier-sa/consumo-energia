import { dataLocalISO, formatarData } from '../utilitarios/datas.js';
import { criarControleFotos } from './fotos.js';

export function criarFormularioLeitura(aoSalvar) {
  let identificador = null;
  const formulario = document.querySelector('#entry-form');
  const mensagem = document.querySelector('#form-message');
  const fotos = criarControleFotos((texto) => mostrarMensagem(texto, 'error'));

  function mostrarMensagem(texto, tipo = '') {
    mensagem.className = `message${tipo ? ` message-${tipo}` : ''}`;
    mensagem.textContent = texto;
  }

  function atualizarConsumo() {
    const manhaTexto = document.querySelector('#morning').value;
    const noiteTexto = document.querySelector('#night').value;
    const completo = manhaTexto !== '' && noiteTexto !== '';
    const valido = completo && Number(noiteTexto) >= Number(manhaTexto);
    document.querySelector('#preview-value').textContent = valido ? Number(noiteTexto) - Number(manhaTexto) : 0;
    document.querySelector('#preview-hint').textContent = !completo ? 'Preencha as duas leituras' : valido ? 'Diferença entre noite e manhã' : 'A leitura da noite deve ser maior';
  }

  function limpar() {
    identificador = null;
    formulario.reset();
    document.querySelector('#date').value = dataLocalISO();
    document.querySelector('#form-mode').textContent = 'Nova leitura';
    document.querySelector('#entry-title').textContent = 'Registrar medidor';
    document.querySelector('#btn-save').textContent = 'Salvar leitura';
    document.querySelector('#btn-clear').classList.add('hidden');
    mostrarMensagem('');
    fotos.limparTodas();
    atualizarConsumo();
  }

  function editar(leitura) {
    identificador = leitura.identificador;
    document.querySelector('#date').value = leitura.data;
    document.querySelector('#morning').value = leitura.leitura_manha;
    document.querySelector('#night').value = leitura.leitura_noite;
    document.querySelector('#form-mode').textContent = 'Editando leitura';
    document.querySelector('#entry-title').textContent = formatarData(leitura.data);
    document.querySelector('#btn-save').textContent = 'Atualizar leitura';
    document.querySelector('#btn-clear').classList.remove('hidden');
    atualizarConsumo();
    document.querySelector('.entry-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  formulario.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    const data = document.querySelector('#date').value;
    const manha = document.querySelector('#morning').value;
    const noite = document.querySelector('#night').value;
    if (!data || manha === '' || noite === '') return mostrarMensagem('Preencha a data e as duas leituras.', 'error');
    if (Number(manha) < 0 || Number(noite) < Number(manha)) return mostrarMensagem('A leitura da noite deve ser igual ou maior que a da manhã.', 'error');

    const dados = new FormData();
    dados.append('date', data); dados.append('morning', manha); dados.append('night', noite);
    fotos.adicionarAoFormulario(dados);
    try {
      const estavaEditando = Boolean(identificador);
      await aoSalvar(identificador, dados);
      limpar();
      mostrarMensagem(estavaEditando ? 'Leitura atualizada com sucesso.' : 'Leitura salva com sucesso.', 'success');
    } catch (erro) {
      mostrarMensagem(erro.message, 'error');
    }
  });
  document.querySelector('#morning').addEventListener('input', atualizarConsumo);
  document.querySelector('#night').addEventListener('input', atualizarConsumo);
  document.querySelector('#btn-clear').addEventListener('click', limpar);
  document.querySelector('#date').value = dataLocalISO();

  return { editar, mostrarMensagem };
}

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
    const turno = document.querySelector('input[name="shift"]:checked').value;
    document.querySelector('.morning-field').classList.toggle('hidden-shift', turno !== 'morning');
    document.querySelector('.night-field').classList.toggle('hidden-shift', turno !== 'night');
    document.querySelector('#morning').disabled = turno !== 'morning';
    document.querySelector('#night').disabled = turno !== 'night';
    document.querySelector('#preview-hint').textContent = `Você está registrando somente o turno da ${turno === 'morning' ? 'manhã' : 'noite'}.`;
  }

  function limpar() {
    identificador = null;
    formulario.reset();
    document.querySelector('#date').value = dataLocalISO();
    document.querySelector('input[name="shift"][value="morning"]').checked = true;
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
    document.querySelector('#morning').value = leitura.leitura_manha ?? '';
    document.querySelector('#night').value = leitura.leitura_noite ?? '';
    const turnoInicial = leitura.leitura_manha !== null && leitura.leitura_manha !== undefined ? 'morning' : 'night';
    document.querySelector(`input[name="shift"][value="${turnoInicial}"]`).checked = true;
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
    const turno = document.querySelector('input[name="shift"]:checked').value;
    const leitura = document.querySelector(`#${turno}`).value;
    if (!data || leitura === '') return mostrarMensagem('Preencha a data e a leitura do turno escolhido.', 'error');
    if (Number(leitura) < 0) return mostrarMensagem('A leitura não pode ser negativa.', 'error');

    const dados = new FormData();
    dados.append('date', data); dados.append('shift', turno); dados.append(turno, leitura);
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
  document.querySelectorAll('input[name="shift"]').forEach((campo) => campo.addEventListener('change', atualizarConsumo));
  document.querySelector('#btn-clear').addEventListener('click', limpar);
  document.querySelector('#date').value = dataLocalISO();
  atualizarConsumo();

  return { editar, mostrarMensagem };
}

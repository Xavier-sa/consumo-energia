import { formatarHora } from '../utilitarios/datas.js';

const limiteBytes = 5 * 1024 * 1024;

export function criarControleFotos(aoErro) {
  const periodos = ['morning', 'night'];

  function limpar(periodo) {
    const entrada = document.querySelector(`#${periodo}-photo`);
    const previa = document.querySelector(`#${periodo}-preview`);
    const imagem = previa.querySelector('img');
    if (imagem.src.startsWith('blob:')) URL.revokeObjectURL(imagem.src);
    entrada.value = '';
    imagem.removeAttribute('src');
    document.querySelector(`#${periodo}-photo-time`).value = '';
    previa.classList.add('hidden');
  }

  function mostrar(periodo, arquivo) {
    if (!arquivo) return limpar(periodo);
    if (arquivo.size > limiteBytes) {
      limpar(periodo);
      aoErro('A foto deve ter no máximo 5 MB.');
      return;
    }
    const instante = new Date().toISOString();
    const previa = document.querySelector(`#${periodo}-preview`);
    previa.querySelector('img').src = URL.createObjectURL(arquivo);
    previa.querySelector('.photo-time').textContent = `Registrada às ${formatarHora(instante)}`;
    document.querySelector(`#${periodo}-photo-time`).value = instante;
    previa.classList.remove('hidden');
  }

  periodos.forEach((periodo) => {
    document.querySelector(`#${periodo}-photo`).addEventListener('change', (evento) => mostrar(periodo, evento.target.files[0]));
  });
  document.querySelectorAll('.remove-photo').forEach((botao) => botao.addEventListener('click', () => limpar(botao.dataset.period)));

  return {
    limparTodas: () => periodos.forEach(limpar),
    adicionarAoFormulario(formulario) {
      periodos.forEach((periodo) => {
        const arquivo = document.querySelector(`#${periodo}-photo`).files[0];
        if (!arquivo) return;
        formulario.append(`${periodo}_photo`, arquivo);
        formulario.append(`${periodo}_photo_time`, document.querySelector(`#${periodo}-photo-time`).value);
      });
    },
  };
}

export function dataLocalISO() {
  const agora = new Date();
  const deslocamento = agora.getTimezoneOffset() * 60000;
  return new Date(agora.getTime() - deslocamento).toISOString().split('T')[0];
}

export function formatarData(data) {
  return new Intl.DateTimeFormat('pt-BR', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'UTC' })
    .format(new Date(`${data}T00:00:00Z`)).replace('.', '');
}

export function formatarHora(instante) {
  if (!instante) return '';
  return new Intl.DateTimeFormat('pt-BR', { hour: '2-digit', minute: '2-digit' }).format(new Date(instante));
}

export function dataExtensoHoje() {
  return new Intl.DateTimeFormat('pt-BR', { weekday: 'long', day: 'numeric', month: 'long' }).format(new Date());
}

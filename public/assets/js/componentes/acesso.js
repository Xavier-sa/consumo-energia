export function criarControleAcesso() {
  function alternar(modo) {
    const cadastro = modo === 'register';
    document.querySelector('#login-form').classList.toggle('hidden', cadastro);
    document.querySelector('#register-form').classList.toggle('hidden', !cadastro);
    document.querySelector('#show-login').setAttribute('aria-selected', String(!cadastro));
    document.querySelector('#show-register').setAttribute('aria-selected', String(cadastro));
    document.querySelector('#auth-description').textContent = cadastro
      ? 'Crie sua conta para ter um histórico privado de consumo.'
      : 'Entre para registrar as leituras do seu medidor.';
    document.querySelector(cadastro ? '#register-username' : '#username').focus();
  }

  document.querySelector('#show-login').addEventListener('click', () => alternar('login'));
  document.querySelector('#show-register').addEventListener('click', () => alternar('register'));
  return { alternar };
}

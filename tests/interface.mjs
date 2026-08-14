import assert from 'node:assert/strict';
import { criarControleAcesso } from '../public/assets/js/componentes/acesso.js';

function elemento() {
  const classes = new Set();
  return {
    atributos: {},
    eventos: {},
    focado: false,
    textContent: '',
    classList: { toggle: (classe, ativo) => ativo ? classes.add(classe) : classes.delete(classe), contains: (classe) => classes.has(classe) },
    setAttribute(nome, valor) { this.atributos[nome] = valor; },
    addEventListener(nome, evento) { this.eventos[nome] = evento; },
    focus() { this.focado = true; },
  };
}

const elementos = Object.fromEntries([
  '#login-form', '#register-form', '#show-login', '#show-register', '#auth-description', '#register-username', '#username',
].map((seletor) => [seletor, elemento()]));
global.document = { querySelector: (seletor) => elementos[seletor] };

const controle = criarControleAcesso();
controle.alternar('register');
assert.equal(elementos['#login-form'].classList.contains('hidden'), true);
assert.equal(elementos['#register-form'].classList.contains('hidden'), false);
assert.equal(elementos['#show-login'].atributos['aria-selected'], 'false');
assert.equal(elementos['#show-register'].atributos['aria-selected'], 'true');
assert.equal(elementos['#register-username'].focado, true);

controle.alternar('login');
assert.equal(elementos['#login-form'].classList.contains('hidden'), false);
assert.equal(elementos['#register-form'].classList.contains('hidden'), true);
assert.equal(elementos['#username'].focado, true);
console.log('✓ alternância entre login e cadastro funciona');

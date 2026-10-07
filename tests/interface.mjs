import assert from 'node:assert/strict';
import { criarControleAcesso } from '../public/assets/js/componentes/acesso.js';
import { criarPainelAdmin, filtrarLeituras, podeAdministrar } from '../public/assets/js/componentes/painel-admin.js';

function elemento() {
  const classes = new Set();
  return {
    atributos: {},
    eventos: {},
    focado: false,
    textContent: '',
    value: '',
    children: [],
    classList: { toggle: (classe, ativo) => ativo ? classes.add(classe) : classes.delete(classe), contains: (classe) => classes.has(classe) },
    setAttribute(nome, valor) { this.atributos[nome] = valor; },
    addEventListener(nome, evento) { this.eventos[nome] = evento; },
    focus() { this.focado = true; },
    append(...itens) { this.children.push(...itens); },
    replaceChildren(...itens) { this.children = itens; },
    get firstChild() { return this.children[0]; },
    showModal() { this.open = true; },
    close() { this.open = false; },
  };
}

const elementos = Object.fromEntries([
  '#login-form', '#register-form', '#show-login', '#show-register', '#auth-description', '#register-username', '#username',
].map((seletor) => [seletor, elemento()]));
global.document = { querySelector: (seletor) => elementos[seletor], querySelectorAll: () => [] };

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

assert.equal(podeAdministrar({ role: 'admin' }), true);
assert.equal(podeAdministrar({ role: 'user' }), false);
const leituras = [{ residencia_id: 1 }, { residencia_id: 2 }, { residencia_id: 1 }];
assert.deepEqual(filtrarLeituras(leituras, '1'), [leituras[0], leituras[2]]);
assert.deepEqual(filtrarLeituras(leituras, ''), leituras);
console.log('✓ painel administrativo respeita papel e filtro por usuário');

const seletoresAdmin = [
  '#btn-admin', '#admin-dialog', '#admin-user-filter', '#admin-message', '#btn-close-admin',
  '#admin-users-table tbody', '#admin-readings-table tbody',
];
const elementosAdmin = Object.fromEntries(seletoresAdmin.map((seletor) => [seletor, elemento()]));
const documentoAdmin = {
  querySelector: (seletor) => elementosAdmin[seletor],
  createElement: () => elemento(),
};
const respostaAdmin = {
  usuarios: [{ username: 'XAVIER', residencia_id: 1, role: 'admin', total_leituras: 1, ultimo_acesso: null }],
  leituras: [{ residencia_id: 1, usuarios: ['XAVIER'], data: '2026-08-14', leitura_manha: 10, leitura_noite: 12, consumo: 2 }],
};
const painel = criarPainelAdmin({ painelAdmin: async () => respostaAdmin }, documentoAdmin);
painel.configurarUsuario({ role: 'user' });
assert.equal(elementosAdmin['#btn-admin'].classList.contains('hidden'), true);
painel.configurarUsuario({ role: 'admin' });
assert.equal(elementosAdmin['#btn-admin'].classList.contains('hidden'), false);
await painel.abrir();
assert.equal(elementosAdmin['#admin-dialog'].open, true);
assert.equal(elementosAdmin['#admin-users-table tbody'].children.length, 1);
assert.equal(elementosAdmin['#admin-readings-table tbody'].children.length, 1);
assert.equal(elementosAdmin['#admin-message'].textContent, '');
elementosAdmin['#btn-close-admin'].eventos.click();
assert.equal(elementosAdmin['#admin-dialog'].open, false);
console.log('✓ botão administrativo abre e preenche o modal');

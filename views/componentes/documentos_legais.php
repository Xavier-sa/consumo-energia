<?php
declare(strict_types=1);

function documentosLegais(): void
{
    ?>
    <dialog id="terms-dialog" class="legal-dialog" aria-labelledby="terms-title">
      <article class="legal-modal">
        <header class="legal-modal-header">
          <div><p class="eyebrow">Documento legal</p><h2 id="terms-title">Termos de Uso</h2></div>
          <button type="button" class="modal-close" data-close-legal aria-label="Fechar Termos de Uso">×</button>
        </header>
        <div class="legal-modal-content" tabindex="0">
          <p class="legal-version"><strong>Versão:</strong> 1.0<br><strong>Vigência:</strong> 7 de outubro de 2026</p>
          <p>Estes Termos regulam o uso do Meu Consumo. Leia também a Política de Privacidade antes de criar uma conta.</p>
          <h3>Objetivo e condições de uso</h3>
          <p>O Meu Consumo permite registrar leituras de um medidor de energia, anexar fotos opcionais, calcular a diferença entre leituras e consultar ou exportar o histórico da residência associada à conta.</p>
          <p>Você deve fornecer informações corretas, usar a aplicação de forma lícita, proteger seus dados de acesso e enviar somente conteúdo que tenha autorização para utilizar.</p>
          <h3>Leituras e disponibilidade</h3>
          <p>As leituras são informadas pelo usuário. O consumo exibido tem finalidade de acompanhamento e não substitui medição, fatura ou informação oficial da distribuidora.</p>
          <p>O serviço pode ficar indisponível para manutenção, correção ou por falhas de infraestrutura. Não há garantia de disponibilidade ininterrupta. O responsável pela instalação deve manter cópias de segurança adequadas.</p>
          <h3>Segurança, propriedade e privacidade</h3>
          <p>Escolha uma senha forte e exclusiva. A aplicação pode recusar operações inválidas ou usos que prejudiquem sua segurança ou o acesso de outras pessoas.</p>
          <p>O código-fonte usa a Licença MIT, que não transfere direitos sobre dados ou fotos enviados. O tratamento de dados pessoais está descrito na Política de Privacidade.</p>
          <h3>Exclusão, atualizações e contato</h3>
          <p>A interface ainda não oferece exclusão de conta. O responsável pela instalação deve definir um procedimento. Dados poderão ser preservados quando houver obrigação legal ou outra hipótese legítima de retenção.</p>
          <p>Novas versões devem indicar sua vigência. Mudanças relevantes para usuários existentes exigem comunicação e, quando necessário, novo aceite; não existe aceite retroativo.</p>
          <p>O projeto ainda não informa um canal jurídico ou de atendimento. O responsável pela instalação deve defini-lo antes do uso em produção.</p>
        </div>
      </article>
    </dialog>

    <dialog id="privacy-dialog" class="legal-dialog" aria-labelledby="privacy-title">
      <article class="legal-modal">
        <header class="legal-modal-header">
          <div><p class="eyebrow">Documento legal</p><h2 id="privacy-title">Política de Privacidade</h2></div>
          <button type="button" class="modal-close" data-close-legal aria-label="Fechar Política de Privacidade">×</button>
        </header>
        <div class="legal-modal-content" tabindex="0">
          <p class="legal-version"><strong>Versão:</strong> 1.0<br><strong>Vigência:</strong> 7 de outubro de 2026</p>
          <p>Esta Política descreve o tratamento feito pelo código atual. A adequação completa à LGPD também depende dos processos de quem instala e opera a aplicação.</p>
          <h3>Dados e finalidades</h3>
          <ul>
            <li>Nome de usuário e senha para identificar e autenticar a conta.</li>
            <li>Identificadores de conta, residência e perfil para separar históricos e controlar permissões.</li>
            <li>Endereço IP e datas de acesso para histórico e apoio à segurança.</li>
            <li>Datas, turnos, valores, consumo e fotos opcionais para documentar as leituras.</li>
            <li>Data e versões dos documentos para registrar o aceite da conta.</li>
          </ul>
          <h3>Bases legais</h3>
          <p>Dados necessários à conta e ao histórico apoiam a execução do serviço solicitado. Registros de segurança podem apoiar legítimo interesse, sujeito à avaliação do responsável pela instalação. Obrigações legais podem justificar retenções específicas.</p>
          <p>O aceite dos documentos não é consentimento genérico. Finalidades opcionais baseadas em consentimento devem ter escolha separada e revogável.</p>
          <h3>Armazenamento, compartilhamento e segurança</h3>
          <p>Contas e leituras ficam em arquivos JSON e fotos ficam na pasta de uploads do servidor. O código não define exclusão automática; o operador deve estabelecer prazos proporcionais.</p>
          <p>Não há integração destinada a compartilhar dados com terceiros. O provedor de hospedagem pode processá-los para operar a infraestrutura. A interface administrativa não retorna senhas, hashes ou IPs.</p>
          <p>O projeto usa hash para novas senhas, separa dados por residência, protege rotas com sessão e restringe uploads. Contas históricas podem manter senha no formato legado até o próximo login, quando ela é convertida para hash.</p>
          <h3>Direitos e solicitações</h3>
          <p>Nos limites da LGPD, você pode solicitar confirmação e acesso, correção, informação sobre compartilhamentos, anonimização, bloqueio ou eliminação nas hipóteses legais e revogação do consentimento quando aplicável.</p>
          <p>A interface ainda não oferece autoatendimento, e o projeto não informa um canal nem identifica o controlador de cada instalação. O operador deve publicar essas informações antes do uso em produção. Não envie dados pessoais em issues públicas.</p>
        </div>
      </article>
    </dialog>
    <?php
}

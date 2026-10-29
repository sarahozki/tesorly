<?php
// Serviços do empresário: os que ele oferece. (O cliente vê os serviços no perfil do profissional.)
require_once RAIZ . '/funcoes.php';

if (empty($ehEmpresario)) {
    echo '<p class="aviso_agenda">Esta área é só para empresários. Para ver os serviços de um profissional, abra o perfil dele em <a href="?pagina=profissionais">Profissionais</a>.</p>';
    return;
}

$servicos = agendaServicosDoProfissional($conexao, $meuIdEmpresario);
?>
<h2 class="titulo_pagina_agenda">Serviços disponíveis</h2>

<?php require RAIZ . '/includes/passos_empresario.php'; ?>

<?php if (!$servicos): ?>
    <p class="aviso_agenda">Você ainda não cadastrou nenhum serviço.</p>
<?php else: ?>
    <div class="grid-servicos_pagprod">
        <?php foreach ($servicos as $s): ?>
            <article class="card-servico_pagprod">
                <?php if (!empty($s['serv_fotos'])): ?>
                    <img src="../uploads/servicos/<?= htmlspecialchars($s['serv_fotos']) ?>" alt="<?= htmlspecialchars($s['serv_nome']) ?>">
                <?php else: ?>
                    <div class="sem_foto_catalogo" aria-hidden="true">✂</div>
                <?php endif; ?>

                <span class="tag_pagprod"><?= htmlspecialchars($s['serv_nome']) ?></span>
                <p class="descricao_pagprod"><?= htmlspecialchars($s['serv_descricao']) ?></p>

                <div class="info_pagprod">
                    <span class="label_pagprod">Duração</span>
                    <span class="valor_pagprod"><?= htmlspecialchars(agendaDuracaoTexto($s['serv_tempo'])) ?></span>
                </div>
                <div class="info_pagprod info-preco_pagprod">
                    <span class="label_pagprod">Valor</span>
                    <span class="preco_pagprod">R$ <?= number_format((float)$s['serv_valor'], 2, ',', '.') ?></span>
                </div>

                <div class="acao_card_catalogo">
                    <a class="botao_agenda botao_sec_agenda" href="?pagina=meus_servicos&amp;editar=<?= (int)$s['idservicos'] ?>#form_servico">Gerenciar</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<button class="btn-add"><a href="?pagina=meus_servicos">Adicionar novo serviço</a> <span>+</span></button>

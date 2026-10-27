<?php
// Catálogo: todos os serviços cadastrados pelos profissionais.
require_once RAIZ . '/funcoes.php';

$servicos = catalogoListarServicos($conexao);
?>
<h2 class="titulo_pagina_agenda">Serviços disponíveis</h2>

<?php if (!$servicos): ?>
    <p class="aviso_agenda">Nenhum serviço cadastrado ainda.</p>
<?php else: ?>
    <div class="grid-servicos_pagprod">
        <?php foreach ($servicos as $s): ?>
            <?php $meu = !empty($ehEmpresario) && (int)$s['idempresario'] === (int)$meuIdEmpresario; ?>
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
                    <span class="label_pagprod">Profissional<br><strong><?= htmlspecialchars($s['profissional']) ?></strong></span>
                    <span class="preco_pagprod">R$ <?= number_format((float)$s['serv_valor'], 2, ',', '.') ?></span>
                </div>

                <div class="acao_card_catalogo">
                    <?php if ($meu): ?>
                        <a class="botao_agenda botao_sec_agenda" href="?pagina=meus_servicos&amp;editar=<?= (int)$s['idservicos'] ?>#form_servico">Gerenciar</a>
                    <?php else: ?>
                        <a class="botao_agenda" href="?pagina=profissional&amp;id=<?= (int)$s['idempresario'] ?>">Agendar</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($ehEmpresario)): ?>
    <button class="btn-add"><a href="?pagina=meus_servicos">Adicionar novo serviço</a> <span>+</span></button>
<?php endif; ?>

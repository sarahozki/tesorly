<?php
require_once RAIZ . '/funcoes.php';

if (!isset($_SESSION['id'])) {
    echo '<p class="aviso_agenda">Faça <a href="../login.php">login</a> para ver sua agenda.</p>';
    return;
}

$idusuario    = (int)$_SESSION['id'];
$meus         = agendaListarPorUsuario($conexao, $idusuario);
$ehProfissional = agendaIdEmpresarioDoUsuario($conexao, $idusuario) !== null;
$recebidos    = $ehProfissional ? agendaListarRecebidos($conexao, $idusuario) : [];
$agora        = date('Y-m-d H:i:s');
?>
<script src="../js/agenda.js" defer></script>

<h2 class="titulo_pagina_agenda">Agenda</h2>

<?php if ($ehProfissional): ?>
    <h3 class="subtitulo_agenda">Agendamentos recebidos</h3>
    <?php if (!$recebidos): ?>
        <p class="aviso_agenda">Nenhum agendamento a partir de hoje.</p>
    <?php else: ?>
        <ul class="lista_agenda">
            <?php foreach ($recebidos as $a): ?>
                <li class="card_agenda item_agenda">
                    <div>
                        <strong><?= date('d/m/Y', strtotime($a['agend_horario'])) ?> às <?= date('H:i', strtotime($a['agend_horario'])) ?></strong>
                        <p><?= htmlspecialchars($a['cliente']) ?> · <?= htmlspecialchars($a['user_telefone'] ?? '') ?></p>
                        <p class="sub_agenda"><?= htmlspecialchars($a['serv_nome']) ?> · <?= htmlspecialchars(agendaDuracaoTexto($a['serv_tempo'])) ?></p>
                    </div>
                    <span class="status_agenda status_<?= htmlspecialchars($a['agend_status']) ?>"><?= htmlspecialchars(agendaStatusTexto($a['agend_status'])) ?></span>
                    <div class="acoes_agenda">
                        <?php if ($a['agend_status'] === 'pendente'): ?>
                            <button type="button" class="botao_agenda btn_status_agenda" data-id="<?= (int)$a['idagenda'] ?>" data-acao="confirmar">Confirmar</button>
                        <?php endif; ?>
                        <?php if (in_array($a['agend_status'], ['pendente', 'confirmado'], true)): ?>
                            <button type="button" class="botao_agenda botao_sec_agenda btn_status_agenda" data-id="<?= (int)$a['idagenda'] ?>" data-acao="cancelar">Cancelar</button>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<h3 class="subtitulo_agenda">Meus agendamentos</h3>
<?php if (!$meus): ?>
    <p class="aviso_agenda">Você ainda não agendou nada. <a href="?pagina=profissionais">Escolha um profissional</a>.</p>
<?php else: ?>
    <ul class="lista_agenda">
        <?php foreach ($meus as $a): ?>
            <li class="card_agenda item_agenda">
                <div>
                    <strong><?= date('d/m/Y', strtotime($a['agend_horario'])) ?> às <?= date('H:i', strtotime($a['agend_horario'])) ?></strong>
                    <p><?= htmlspecialchars($a['profissional']) ?> · <?= htmlspecialchars($a['empre_tipo']) ?></p>
                    <p class="sub_agenda"><?= htmlspecialchars($a['serv_nome']) ?> · <?= htmlspecialchars(agendaDuracaoTexto($a['serv_tempo'])) ?> · R$ <?= number_format((float)$a['serv_valor'], 2, ',', '.') ?></p>
                </div>
                <span class="status_agenda status_<?= htmlspecialchars($a['agend_status']) ?>"><?= htmlspecialchars(agendaStatusTexto($a['agend_status'])) ?></span>
                <div class="acoes_agenda">
                    <?php if (in_array($a['agend_status'], ['pendente', 'confirmado'], true) && $a['agend_horario'] > $agora): ?>
                        <button type="button" class="botao_agenda botao_sec_agenda btn_status_agenda" data-id="<?= (int)$a['idagenda'] ?>" data-acao="cancelar">Cancelar</button>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

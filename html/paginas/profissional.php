<?php
// Perfil do profissional + agenda para o cliente marcar horário (de 30 em 30 min)
require_once RAIZ . '/funcoes.php';

if (!isset($_SESSION['id'])) {
    echo '<p class="aviso_agenda">Faça <a href="../login.php">login</a> para agendar.</p>';
    return;
}

$idempresario = (int)($_GET['id'] ?? 0);
$profissional = agendaBuscarProfissional($conexao, $idempresario);

if (!$profissional) {
    echo '<p class="aviso_agenda">Profissional não encontrado. <a href="?pagina=profissionais">Voltar</a></p>';
    return;
}

$servicos   = agendaServicosDoProfissional($conexao, $idempresario);
$expediente = agendaExpediente($conexao, $idempresario);
$foto = !empty($profissional['user_fotos']) ? '../uploads/usuarios/' . $profissional['user_fotos'] : '../imagens/fotouser.png';

$nomesDia = ['dom' => 'Dom', 'seg' => 'Seg', 'ter' => 'Ter', 'qua' => 'Qua', 'qui' => 'Qui', 'sex' => 'Sex', 'sab' => 'Sáb'];
$nomesMes = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
?>
<script src="../js/agenda.js" defer></script>

<section class="perfil_agenda">

    <div class="card_agenda cabecalho_prof_agenda">
        <img class="foto_prof_agenda" src="<?= htmlspecialchars($foto) ?>" alt="Foto de <?= htmlspecialchars($profissional['user_nome']) ?>">
        <div>
            <h2><?= htmlspecialchars($profissional['user_nome']) ?></h2>
            <p class="sub_agenda"><?= htmlspecialchars($profissional['empre_tipo']) ?> · <?= htmlspecialchars($profissional['empre_cidade']) ?></p>
        </div>
        <a class="link_voltar_agenda" href="?pagina=profissionais">← Profissionais</a>
    </div>

    <?php if (!$servicos): ?>
        <p class="aviso_agenda">Este profissional ainda não cadastrou serviços.</p>
    <?php elseif (!$expediente): ?>
        <p class="aviso_agenda">Este profissional ainda não definiu os horários de atendimento.</p>
    <?php else: ?>

    <div class="card_agenda" id="agenda_prof" data-idempresario="<?= (int)$idempresario ?>">
        <h3 class="titulo_agenda">Agendar horário</h3>

        <label class="rotulo_agenda" for="agenda_servico">Serviço</label>
        <select id="agenda_servico" class="select_agenda">
            <?php foreach ($servicos as $s): ?>
                <option value="<?= (int)$s['idservicos'] ?>">
                    <?= htmlspecialchars($s['serv_nome']) ?> — <?= htmlspecialchars(agendaDuracaoTexto($s['serv_tempo'])) ?> — R$ <?= number_format((float)$s['serv_valor'], 2, ',', '.') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p class="rotulo_agenda">Dia</p>
        <div class="dias_agenda">
            <?php for ($i = 0; $i < 14; $i++): ?>
                <?php
                    $dia = new DateTime('today');
                    $dia->modify('+' . $i . ' day');
                    $dataIso = $dia->format('Y-m-d');
                    $codigo = agendaCodigoDia($dataIso);
                    $atende = isset($expediente[$codigo]);
                ?>
                <button type="button" class="dia_btn_agenda" data-data="<?= $dataIso ?>" <?= $atende ? '' : 'disabled title="Não atende neste dia"' ?>>
                    <span><?= $nomesDia[$codigo] ?></span>
                    <strong><?= $dia->format('d') ?></strong>
                    <span><?= $nomesMes[(int)$dia->format('n') - 1] ?></span>
                </button>
            <?php endfor; ?>
        </div>

        <p class="rotulo_agenda">Horários <span class="legenda_agenda">de 30 em 30 minutos · riscados já estão ocupados</span></p>
        <div class="horarios_agenda" id="agenda_horarios">
            <p class="aviso_agenda">Escolha um dia.</p>
        </div>

        <div class="rodape_agenda">
            <p id="agenda_resumo" class="resumo_agenda"></p>
            <button type="button" class="botao_agenda" id="agenda_confirmar" disabled>Confirmar agendamento</button>
            <p id="agenda_msg" class="msg_agenda" role="status"></p>
        </div>
    </div>

    <?php endif; ?>
</section>

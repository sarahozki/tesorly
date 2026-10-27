<?php
// Área do empresário: definir os dias e horários de atendimento (a agenda do cliente é de 30 em 30 min).
require_once RAIZ . '/funcoes.php';

if (empty($ehEmpresario)) {
    echo '<p class="aviso_agenda">Esta área é só para empresários.</p>';
    return;
}

$flash = painelFlashLer();
$digitado = (isset($flash['dados']['dia']) && is_array($flash['dados']['dia'])) ? $flash['dados']['dia'] : null;
$salvo = painelHorariosParaFormulario($conexao, $meuIdEmpresario);

// sugestão para dias que ainda não têm horário
$padrao = ['t1_ini' => '09:00', 't1_fim' => '12:00', 't2_ini' => '13:30', 't2_fim' => '18:00'];
?>
<script src="../js/painel.js" defer></script>

<h2 class="titulo_pagina_agenda">Meus horários</h2>

<?php if ($flash): ?>
    <p class="flash_painel flash_<?= htmlspecialchars($flash['tipo']) ?>" role="status"><?= htmlspecialchars($flash['texto']) ?></p>
<?php endif; ?>

<?php require RAIZ . '/includes/passos_empresario.php'; ?>

<form id="form_horarios" class="card_agenda form_painel" method="post" action="../saves/save_horarios.php">
    <h3 class="subtitulo_painel">Dias e horários de atendimento</h3>
    <p class="sub_agenda">
        Marque os dias em que você atende. Os clientes vão ver horários de 30 em 30 minutos dentro de cada turno.
        Use o segundo turno para a volta do almoço (ou deixe em branco).
    </p>

    <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">

    <div class="tabela_scroll_painel">
        <table class="tabela_horarios_painel">
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Atendo</th>
                    <th>Turno 1</th>
                    <th>Turno 2 <span class="opcional_painel">(opcional)</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (painelDiasSemana() as $codigo => $info): ?>
                    <?php
                        if ($digitado !== null) {
                            $ativo = !empty($digitado[$codigo]['ativo']);
                            $v = is_array($digitado[$codigo] ?? null) ? $digitado[$codigo] : [];
                            $v = array_merge($padrao, $v);
                        } else {
                            $ativo = isset($salvo[$codigo]);
                            $v = $ativo ? $salvo[$codigo] : $padrao;
                        }
                    ?>
                    <tr data-dia="<?= $codigo ?>">
                        <td><strong><?= htmlspecialchars($info['nome']) ?></strong></td>
                        <td>
                            <input type="checkbox" name="dia[<?= $codigo ?>][ativo]" value="1" aria-label="Atendo <?= htmlspecialchars($info['nome']) ?>" <?= $ativo ? 'checked' : '' ?>>
                        </td>
                        <td class="turno_painel">
                            <input type="time" step="1800" name="dia[<?= $codigo ?>][t1_ini]" value="<?= htmlspecialchars($v['t1_ini']) ?>" aria-label="Início do turno 1">
                            <span>às</span>
                            <input type="time" step="1800" name="dia[<?= $codigo ?>][t1_fim]" value="<?= htmlspecialchars($v['t1_fim']) ?>" aria-label="Fim do turno 1">
                        </td>
                        <td class="turno_painel">
                            <input type="time" step="1800" name="dia[<?= $codigo ?>][t2_ini]" value="<?= htmlspecialchars($v['t2_ini']) ?>" aria-label="Início do turno 2">
                            <span>às</span>
                            <input type="time" step="1800" name="dia[<?= $codigo ?>][t2_fim]" value="<?= htmlspecialchars($v['t2_fim']) ?>" aria-label="Fim do turno 2">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="botoes_painel">
        <button type="submit" class="botao_agenda">Salvar horários</button>
        <button type="button" class="botao_agenda botao_sec_agenda" id="copiar_segunda">Repetir a segunda nos dias úteis</button>
    </div>
    <p class="sub_agenda">Agendamentos que os clientes já fizeram não são alterados.</p>
</form>

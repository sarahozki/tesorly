<?php
// Área do empresário: cadastrar, editar e excluir os próprios serviços.
require_once RAIZ . '/funcoes.php';

if (empty($ehEmpresario)) {
    echo '<p class="aviso_agenda">Esta área é só para empresários.</p>';
    return;
}

$flash = painelFlashLer();
$dados = $flash['dados'] ?? [];

// Está editando um serviço? (link "Editar" da lista, ou voltou de um erro ao editar)
$idEditar = (int)($_GET['editar'] ?? ($dados['id'] ?? 0));
$editando = $idEditar > 0 ? servicoBuscarDoEmpresario($conexao, $idEditar, $meuIdEmpresario) : null;

// Valores do formulário: o que a pessoa acabou de digitar (se deu erro) > serviço sendo editado > vazio
$valores = [
    'nome'      => $dados['nome']      ?? ($editando['serv_nome'] ?? ''),
    'descricao' => $dados['descricao'] ?? ($editando['serv_descricao'] ?? ''),
    'valor'     => $dados['valor']     ?? ($editando ? number_format((float)$editando['serv_valor'], 2, '.', '') : ''),
    'duracao'   => (int)($dados['duracao'] ?? ($editando ? agendaMinutos($editando['serv_tempo']) : 60)),
];

$opcoesDuracao = painelOpcoesDuracao();
if (!isset($opcoesDuracao[$valores['duracao']]) && $valores['duracao'] > 0) {
    // serviço antigo com duração fora do passo de 30 min (ex.: 45 min): a agenda reserva o próximo bloco de 30
    $reservado = agendaBlocos(agendaHoraTexto($valores['duracao'])) * AGENDA_INTERVALO_MIN;
    $opcoesDuracao[$valores['duracao']] = agendaDuracaoTexto(agendaHoraTexto($valores['duracao']))
        . ' (a agenda reserva ' . agendaDuracaoTexto(agendaHoraTexto($reservado)) . ')';
    ksort($opcoesDuracao);
}

$servicos = agendaServicosDoProfissional($conexao, $meuIdEmpresario);
?>
<script src="../js/painel.js" defer></script>

<h2 class="titulo_pagina_agenda">Meus serviços</h2>

<?php if ($flash): ?>
    <p class="flash_painel flash_<?= htmlspecialchars($flash['tipo']) ?>" role="status"><?= htmlspecialchars($flash['texto']) ?></p>
<?php endif; ?>

<?php require RAIZ . '/includes/passos_empresario.php'; ?>

<form id="form_servico" class="card_agenda form_painel" method="post" action="../saves/save_servico.php" enctype="multipart/form-data">
    <h3 class="subtitulo_painel"><?= $editando ? 'Editar serviço' : 'Novo serviço' ?></h3>

    <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="acao" value="<?= $editando ? 'editar' : 'criar' ?>">
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int)$editando['idservicos'] ?>">
    <?php endif; ?>

    <div class="campo_painel">
        <label for="serv_nome">Nome do serviço</label>
        <input type="text" id="serv_nome" name="nome" maxlength="100" required placeholder="Ex.: Corte feminino" value="<?= htmlspecialchars($valores['nome']) ?>">
    </div>

    <div class="campo_painel">
        <label for="serv_descricao">Descrição</label>
        <textarea id="serv_descricao" name="descricao" rows="3" maxlength="255" required placeholder="O que está incluso no serviço"><?= htmlspecialchars($valores['descricao']) ?></textarea>
    </div>

    <div class="linha_campos_painel">
        <div class="campo_painel">
            <label for="serv_duracao">Duração</label>
            <select id="serv_duracao" name="duracao" required>
                <?php foreach ($opcoesDuracao as $minutos => $texto): ?>
                    <option value="<?= (int)$minutos ?>" <?= $minutos === $valores['duracao'] ? 'selected' : '' ?>><?= htmlspecialchars($texto) ?></option>
                <?php endforeach; ?>
            </select>
            <small>A agenda trabalha em blocos de 30 minutos.</small>
        </div>

        <div class="campo_painel">
            <label for="serv_valor">Valor (R$)</label>
            <input type="number" id="serv_valor" name="valor" min="0.01" step="0.01" required placeholder="0,00" value="<?= htmlspecialchars((string)$valores['valor']) ?>">
        </div>
    </div>

    <div class="campo_painel">
        <label for="serv_foto">Foto do serviço <span class="opcional_painel">(opcional — JPG, PNG ou WEBP, até 2 MB)</span></label>
        <?php if ($editando && !empty($editando['serv_fotos'])): ?>
            <div class="foto_atual_painel">
                <img src="../uploads/servicos/<?= htmlspecialchars($editando['serv_fotos']) ?>" alt="Foto atual do serviço">
                <small>Foto atual. Envie outra só se quiser trocar.</small>
            </div>
        <?php endif; ?>
        <input type="file" id="serv_foto" name="foto" accept="image/jpeg,image/png,image/webp">
    </div>

    <div class="botoes_painel">
        <button type="submit" class="botao_agenda"><?= $editando ? 'Salvar alterações' : 'Cadastrar serviço' ?></button>
        <?php if ($editando): ?>
            <a class="botao_agenda botao_sec_agenda" href="?pagina=meus_servicos">Cancelar edição</a>
        <?php endif; ?>
    </div>
</form>

<h3 class="subtitulo_agenda">Serviços cadastrados (<?= count($servicos) ?>)</h3>

<?php if (!$servicos): ?>
    <p class="aviso_agenda">Você ainda não tem serviços. Cadastre o primeiro no formulário acima.</p>
<?php else: ?>
    <ul class="lista_agenda">
        <?php foreach ($servicos as $s): ?>
            <li class="card_agenda item_servico_painel">
                <?php if (!empty($s['serv_fotos'])): ?>
                    <img class="foto_servico_painel" src="../uploads/servicos/<?= htmlspecialchars($s['serv_fotos']) ?>" alt="">
                <?php else: ?>
                    <div class="foto_servico_painel foto_vazia_painel" aria-hidden="true">✂</div>
                <?php endif; ?>

                <div class="texto_servico_painel">
                    <strong><?= htmlspecialchars($s['serv_nome']) ?></strong>
                    <p><?= htmlspecialchars($s['serv_descricao']) ?></p>
                    <p class="sub_agenda"><?= htmlspecialchars(agendaDuracaoTexto($s['serv_tempo'])) ?> · R$ <?= number_format((float)$s['serv_valor'], 2, ',', '.') ?></p>
                </div>

                <div class="acoes_agenda">
                    <a class="botao_agenda botao_sec_agenda" href="?pagina=meus_servicos&amp;editar=<?= (int)$s['idservicos'] ?>#form_servico">Editar</a>
                    <form method="post" action="../saves/save_servico.php" data-confirmar="Excluir o serviço “<?= htmlspecialchars($s['serv_nome'], ENT_QUOTES) ?>”?">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
                        <input type="hidden" name="acao" value="excluir">
                        <input type="hidden" name="id" value="<?= (int)$s['idservicos'] ?>">
                        <button type="submit" class="botao_agenda botao_perigo_painel">Excluir</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

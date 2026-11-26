<?php
require_once RAIZ . '/funcoes.php';

$cidade = is_string($_GET['cidade'] ?? null) ? trim($_GET['cidade']) : '';
$nichoFiltro = is_string($_GET['nicho'] ?? null) ? trim($_GET['nicho']) : '';
$cidades = agendaListarCidades($conexao);
$nichosDisponiveis = agendaListarNichos();
$profissionais = agendaListarProfissionais($conexao, $cidade, $nichoFiltro);
?>
<h2 class="titulo_pagina_agenda">Profissionais</h2>

<!-- Filtros por Categoria -->
<nav class="tabs_nichos_agenda" aria-label="Filtrar profissionais por categoria">
    <a href="?pagina=profissionais<?= $cidade !== '' ? '&amp;cidade=' . urlencode($cidade) : '' ?>"
       class="tab_nicho_btn <?= $nichoFiltro === '' ? 'ativo' : '' ?>">
       Todos
    </a>
    <?php foreach ($nichosDisponiveis as $slug => $info): ?>
        <?php $ativo = ($nichoFiltro === $slug); ?>
        <a href="?pagina=profissionais&amp;nicho=<?= $slug ?><?= $cidade !== '' ? '&amp;cidade=' . urlencode($cidade) : '' ?>"
           class="tab_nicho_btn <?= $ativo ? 'ativo' : '' ?> tab_nicho_<?= $slug ?>">
           <?= htmlspecialchars($info['nome']) ?>
        </a>
    <?php endforeach; ?>
</nav>

<form class="card_agenda filtro_prof_agenda" method="get" action="">
    <input type="hidden" name="pagina" value="profissionais">
    <?php if ($nichoFiltro !== ''): ?>
        <input type="hidden" name="nicho" value="<?= htmlspecialchars($nichoFiltro) ?>">
    <?php endif; ?>

    <div class="campo_filtro_agenda">
        <label for="filtro_cidade">Buscar por cidade</label>
        <input type="search" id="filtro_cidade" name="cidade" list="lista_cidades" maxlength="100" autocomplete="off"
               placeholder="Digite o nome da cidade" value="<?= htmlspecialchars($cidade) ?>" onchange="this.form.submit()">
        <datalist id="lista_cidades">
            <?php foreach ($cidades as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>"></option>
            <?php endforeach; ?>
        </datalist>
    </div>

    <button type="submit" class="botao_agenda">Buscar</button>
    <?php if ($cidade !== '' || $nichoFiltro !== ''): ?>
        <a class="botao_agenda botao_sec_agenda" href="?pagina=profissionais">Limpar</a>
    <?php endif; ?>
</form>

<?php if ($cidade !== '' || $nichoFiltro !== ''): ?>
    <p class="sub_agenda resultado_filtro_agenda">
        <?= count($profissionais) ?> profissiona<?= count($profissionais) === 1 ? 'l' : 'is' ?>
        <?php if ($nichoFiltro !== ''): ?> do ramo <strong><?= htmlspecialchars($nichosDisponiveis[$nichoFiltro]['nome'] ?? $nichoFiltro) ?></strong><?php endif; ?>
        <?php if ($cidade !== ''): ?> em “<?= htmlspecialchars($cidade) ?>”<?php endif; ?>
    </p>
<?php endif; ?>

<?php if (!$profissionais): ?>
    <p class="aviso_agenda">
        <?php if ($cidade !== '' || $nichoFiltro !== ''): ?>
            Nenhum profissional encontrado com os filtros aplicados. <a href="?pagina=profissionais">Ver todos</a>
        <?php else: ?>
            Nenhum profissional cadastrado ainda.
        <?php endif; ?>
    </p>
<?php else: ?>
    <div class="grid_prof_agenda">
        <?php foreach ($profissionais as $p): ?>
            <?php 
                $foto = !empty($p['user_fotos']) ? '../uploads/usuarios/' . $p['user_fotos'] : '../imagens/fotouser.png'; 
                $nichoP = agendaObterNicho($p);
                $infoP = agendaInfoNicho($nichoP);
            ?>
            <article class="card_agenda card_prof_agenda">
                <div class="card_prof_topo">
                    <span class="badge_nicho_card badge_nicho_<?= $nichoP ?>">
                        <?= htmlspecialchars($infoP['nome']) ?>
                    </span>
                </div>
                <img class="foto_prof_agenda" src="<?= htmlspecialchars($foto) ?>" alt="Foto de <?= htmlspecialchars($p['user_nome']) ?>">
                <h3><?= htmlspecialchars($p['user_nome']) ?></h3>
                <p class="sub_agenda"><?= htmlspecialchars($p['empre_tipo'] ?? $infoP['nome']) ?> · <?= htmlspecialchars($p['empre_cidade']) ?></p>
                <a class="botao_agenda" href="?pagina=profissional&amp;id=<?= (int)$p['idempresario'] ?>">Ver agenda</a>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

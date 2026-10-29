<?php
require_once RAIZ . '/funcoes.php';

 // Verifica se o valor da cidade é nulo ou uma string, e remove espaços em branco do início e do fim
$cidade        = is_string($_GET['cidade'] ?? null) ? trim($_GET['cidade']) : '';
$cidades       = agendaListarCidades($conexao);
$profissionais = agendaListarProfissionais($conexao, $cidade);
?>
<h2 class="titulo_pagina_agenda">Profissionais</h2>

<form class="card_agenda filtro_prof_agenda" method="get" action="">
    <input type="hidden" name="pagina" value="profissionais">

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
    <?php if ($cidade !== ''): ?>
        <a class="botao_agenda botao_sec_agenda" href="?pagina=profissionais">Limpar</a>
    <?php endif; ?>
</form>

<?php if ($cidade !== ''): ?>
    <p class="sub_agenda resultado_filtro_agenda">
        <?= count($profissionais) ?> profissiona<?= count($profissionais) === 1 ? 'l' : 'is' ?> em “<?= htmlspecialchars($cidade) ?>”
    </p>
<?php endif; ?>

<?php if (!$profissionais): ?>
    <p class="aviso_agenda">
        <?php if ($cidade !== ''): ?>
            Nenhum profissional encontrado em “<?= htmlspecialchars($cidade) ?>”. <a href="?pagina=profissionais">Ver todos</a>
        <?php else: ?>
            Nenhum profissional cadastrado ainda.
        <?php endif; ?>
    </p>
<?php else: ?>
    <div class="grid_prof_agenda">
        <?php foreach ($profissionais as $p): ?>
            <?php $foto = !empty($p['user_fotos']) ? '../uploads/usuarios/' . $p['user_fotos'] : '../imagens/fotouser.png'; ?>
            <article class="card_agenda card_prof_agenda">
                <img class="foto_prof_agenda" src="<?= htmlspecialchars($foto) ?>" alt="Foto de <?= htmlspecialchars($p['user_nome']) ?>">
                <h3><?= htmlspecialchars($p['user_nome']) ?></h3>
                <p class="sub_agenda"><?= htmlspecialchars($p['empre_tipo']) ?> · <?= htmlspecialchars($p['empre_cidade']) ?></p>
                <a class="botao_agenda" href="?pagina=profissional&amp;id=<?= (int)$p['idempresario'] ?>">Ver agenda</a>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

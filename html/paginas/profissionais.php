<?php
require_once RAIZ . '/funcoes.php';

$profissionais = agendaListarProfissionais($conexao);
?>
<h2 class="titulo_pagina_agenda">Profissionais</h2>

<?php if (!$profissionais): ?>
    <p class="aviso_agenda">Nenhum profissional cadastrado ainda.</p>
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

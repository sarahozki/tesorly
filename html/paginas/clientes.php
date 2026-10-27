<?php
// Área do empresário: clientes que já agendaram com ele (nome e telefone para contato).
require_once RAIZ . '/funcoes.php';

if (empty($ehEmpresario)) {
    echo '<p class="aviso_agenda">Esta área é só para empresários.</p>';
    return;
}

$clientes = agendaListarClientesDoEmpresario($conexao, $meuIdEmpresario);
?>
<h2 class="titulo_pagina_agenda">Meus clientes</h2>

<?php if (!$clientes): ?>
    <p class="aviso_agenda">Ainda não há clientes com agendamento. Eles aparecem aqui assim que alguém agendar com você.</p>
<?php else: ?>
    <ul class="lista_agenda">
        <?php foreach ($clientes as $c): ?>
            <?php
                $foto = !empty($c['user_fotos']) ? '../uploads/usuarios/' . $c['user_fotos'] : '../imagens/fotouser.png';
                $telefone = (string)($c['user_telefone'] ?? '');
                $soNumeros = preg_replace('/\D/', '', $telefone);
            ?>
            <li class="card_agenda item_agenda">
                <img class="avatar_cliente_painel" src="<?= htmlspecialchars($foto) ?>" alt="">
                <div>
                    <strong><?= htmlspecialchars($c['user_nome']) ?></strong>
                    <p>
                        <?php if ($soNumeros !== ''): ?>
                            <a class="link_tel_painel" href="tel:<?= htmlspecialchars($soNumeros) ?>"><?= htmlspecialchars($telefone) ?></a>
                        <?php else: ?>
                            Sem telefone cadastrado
                        <?php endif; ?>
                    </p>
                    <p class="sub_agenda">
                        <?= (int)$c['total'] ?> agendamento<?= (int)$c['total'] === 1 ? '' : 's' ?>
                        · mais recente: <?= date('d/m/Y', strtotime($c['mais_recente'])) ?> às <?= date('H:i', strtotime($c['mais_recente'])) ?>
                    </p>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

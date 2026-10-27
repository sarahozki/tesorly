<?php
$pagina = $pagina ?? ($_GET['pagina'] ?? 'produtos');
$ehEmpresario = $ehEmpresario ?? false;

// Menu de todo mundo (cliente e empresário)
$menuGeral = [
    'servicos'      => 'Serviços disponíveis',
    'agenda'        => 'Agenda',
    'avalie'        => 'Avalie',
    'perfil'        => 'Meu perfil',
    'profissionais' => 'Profissionais',
];

// Menu SÓ do empresário (cliente não vê e não consegue abrir: o app/index.php também bloqueia)
$menuEmpresario = [
    'meus_servicos' => 'Meus serviços',
    'meus_horarios' => 'Meus horários',
    'clientes'      => 'Clientes',
];

// o perfil de um profissional ("profissional") conta como "Profissionais" no menu
$paginaMenu = ($pagina === 'profissional') ? 'profissionais' : $pagina;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/agenda.css">
    <title>Tesorly</title>
</head>
<body>
    <div class="page_pagprod">
        <header class="topo_pagprod">
            <span class="logo_pagprod">Tesorly</span>
            <button class="chat-btn" aria-label="Chat">💬</button>
        </header>

        <div class="conteudo_pagprod">
            <nav class="sidebar_pagprod">
                <ul>
                    <?php foreach ($menuGeral as $chave => $texto): ?>
                        <li><a href="?pagina=<?= $chave ?>" class="<?= $paginaMenu === $chave ? 'ativo' : '' ?>"><?= $texto ?></a></li>
                    <?php endforeach; ?>

                    <?php if ($ehEmpresario): ?>
                        <li class="menu_titulo_agenda">Área do profissional</li>
                        <?php foreach ($menuEmpresario as $chave => $texto): ?>
                            <li><a href="?pagina=<?= $chave ?>" class="<?= $paginaMenu === $chave ? 'ativo' : '' ?>"><?= $texto ?></a></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <a href="../deslogar.php" class="logout_pagprod">Logout</a>
            </nav>

            <main class="catalogo_pagprod">
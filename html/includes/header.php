<?php
$pagina = $pagina ?? ($_GET['pagina'] ?? 'produtos');
$ehEmpresario = $ehEmpresario ?? false;

$menuGeral = [
    'agenda'        => 'Agenda',
    'avalie'        => 'Avalie',
    'perfil'        => 'Meu perfil',
    'profissionais' => 'Profissionais',
];

$menuEmpresario = [
    'servicos'      => 'Serviços disponíveis',
    'meus_horarios' => 'Meus horários',
    'clientes'      => 'Clientes',
];

$paginaMenu = $pagina;
if ($pagina === 'profissional') { $paginaMenu = 'profissionais'; }   // perfil de um profissional
if ($pagina === 'meus_servicos') { $paginaMenu = 'servicos'; }       // formulário de serviço
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
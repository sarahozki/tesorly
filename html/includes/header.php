<?php
$pagina = $pagina ?? ($_GET['pagina'] ?? 'produtos');
$ehEmpresario = $ehEmpresario ?? false;
$idProfissionalParam = (int)($_GET['id'] ?? 0);

$nichoTema = agendaDeterminarTemaAtivo(
    $conexao,
    $pagina,
    $idProfissionalParam,
    $ehEmpresario,
    $meuIdEmpresario ?? null
);
$infoNichoTema = agendaInfoNicho($nichoTema);

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
    <link rel="stylesheet" href="../css/style.css?v=<?= file_exists(RAIZ . '/css/style.css') ? filemtime(RAIZ . '/css/style.css') : 1 ?>">
    <link rel="stylesheet" href="../css/agenda.css?v=<?= file_exists(RAIZ . '/css/agenda.css') ? filemtime(RAIZ . '/css/agenda.css') : 1 ?>">
    <title>Tesorly · <?= htmlspecialchars($infoNichoTema['nome']) ?></title>
</head>
<body class="tema-<?= htmlspecialchars($nichoTema) ?>">
    <div class="page_pagprod">
        <header class="topo_pagprod">
            <div class="topo_marca_box">
                <a href="?pagina=agenda" class="logo_pagprod">Tesorly</a>
                <span class="badge_nicho_topo"><?= htmlspecialchars($infoNichoTema['nome']) ?></span>
            </div>
            <button class="chat-btn" aria-label="Atendimento">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </button>
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
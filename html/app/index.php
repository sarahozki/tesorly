<?php
session_start();
define('RAIZ', dirname(__DIR__));

if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

require_once RAIZ . '/funcoes.php';

//verifica se é empresário ou cliente
$meuIdEmpresario = agendaIdEmpresarioDoUsuario($conexao, (int)$_SESSION['id']);
$ehEmpresario = ($meuIdEmpresario !== null);

// Página pedida na URL
$pagina = $_GET['pagina'] ?? 'produtos';
if (!is_string($pagina)) {
    $pagina = 'produtos';
}

$paginasPermitidas = ['agenda', 'avalie', 'perfil', 'profissionais', 'profissional', 'editar_perfil'];
$paginasEmpresario = ['servicos', 'meus_servicos', 'meus_horarios', 'clientes'];

if ($ehEmpresario) {
    $paginasPermitidas = array_merge($paginasPermitidas, $paginasEmpresario);
}

if (!in_array($pagina, $paginasPermitidas, true)) {
    $pagina = 'agenda'; // se a página não existir ou não for permitida, vai pr  a agenda
}

$arquivoPagina = RAIZ . '/paginas/' . $pagina . '.php';

require RAIZ . '/includes/header.php';

if (file_exists($arquivoPagina)) {
    include $arquivoPagina;
} else {
    echo "<p>Página não encontrada: {$arquivoPagina}</p>";
}

require RAIZ . '/includes/footer.php';
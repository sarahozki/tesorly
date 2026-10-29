<?php
session_start();
// Caminho absoluto da pasta "html" (uma pasta acima de "app")
define('RAIZ', dirname(__DIR__));

// O app é só para quem fez login
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit;
}

require_once RAIZ . '/funcoes.php';

// Quem entrou é cliente ou empresário? ($meuIdEmpresario fica null para cliente)
$meuIdEmpresario = agendaIdEmpresarioDoUsuario($conexao, (int)$_SESSION['id']);
$ehEmpresario = ($meuIdEmpresario !== null);

// Página pedida na URL, com valor padrão
$pagina = $_GET['pagina'] ?? 'produtos';
if (!is_string($pagina)) {
    $pagina = 'produtos';
}

// Páginas que todo mundo (cliente e empresário) pode abrir
$paginasPermitidas = ['agenda', 'avalie', 'perfil', 'profissionais', 'profissional', 'editar_perfil'];

// Páginas SÓ do empresário (no menu ficam em "Área do profissional").
// O cliente vê os serviços dentro do perfil do profissional.
$paginasEmpresario = ['servicos', 'meus_servicos', 'meus_horarios', 'clientes'];

if ($ehEmpresario) {
    $paginasPermitidas = array_merge($paginasPermitidas, $paginasEmpresario);
}

if (!in_array($pagina, $paginasPermitidas, true)) {
    $pagina = 'agenda'; // página que não existe (ou cliente tentando abrir uma de empresário): cai na Agenda
}

$arquivoPagina = RAIZ . '/paginas/' . $pagina . '.php';

require RAIZ . '/includes/header.php';

if (file_exists($arquivoPagina)) {
    include $arquivoPagina;
} else {
    echo "<p>Página não encontrada: {$arquivoPagina}</p>";
}

require RAIZ . '/includes/footer.php';
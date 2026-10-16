<?php
session_start();
// Caminho absoluto da pasta "html" (uma pasta acima de "app")
define('RAIZ', dirname(__DIR__));

// Página pedida na URL, com valor padrão
$pagina = $_GET['pagina'] ?? 'produtos';

// Lista de páginas permitidas
$paginasPermitidas = ['servicos', 'agenda', 'avalie', 'perfil', 'profissionais', 'clientes', 'editar_perfil'];

if (!in_array($pagina, $paginasPermitidas)) {
    $pagina = 'servicos'; // se a página não existir na lista, cai no padrão
}

$arquivoPagina = RAIZ . '/paginas/' . $pagina . '.php';

require RAIZ . '/includes/header.php';

if (file_exists($arquivoPagina)) {
    include $arquivoPagina;
} else {
    echo "<p>Página não encontrada: {$arquivoPagina}</p>";
}

require RAIZ . '/includes/footer.php';
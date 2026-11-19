<?php
ob_start();
session_start();
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../funcoes.php';

verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../app/index.php?pagina=avalie');
    exit;
}

if (!csrfValido($_POST['csrf'] ?? '')) {
    painelFlashDefinir('erro', 'Sessão expirada. Por favor, tente novamente.');
    header('Location: ../app/index.php?pagina=avalie');
    exit;
}

$idUsuario = (int)($_SESSION['id'] ?? $_SESSION['id_usuario'] ?? 0);
$acao = $_POST['acao'] ?? 'criar';

if ($acao === 'excluir') {
    $idcomentarios = (int)($_POST['idcomentarios'] ?? 0);
    if ($idcomentarios > 0 && deletarcomentarios($conexao, $idcomentarios, $idUsuario)) {
        painelFlashDefinir('ok', 'Avaliação excluída com sucesso.');
    } else {
        painelFlashDefinir('erro', 'Erro ao excluir avaliação ou você não tem permissão.');
    }
    header('Location: ../app/index.php?pagina=avalie');
    exit;
}

$coment_texto = trim($_POST['coment_texto'] ?? $_POST['coment_text'] ?? '');
$comentarios_estrela = (int)($_POST['comentarios_estrela'] ?? 5);

if ($coment_texto === '' || $idUsuario <= 0) {
    painelFlashDefinir('erro', 'Por favor, escreva um comentário antes de enviar.');
    header('Location: ../app/index.php?pagina=avalie');
    exit;
}

if (mb_strlen($coment_texto) > 255) {
    painelFlashDefinir('erro', 'O comentário não pode ter mais de 255 caracteres.');
    header('Location: ../app/index.php?pagina=avalie');
    exit;
}

if ($comentarios_estrela < 1 || $comentarios_estrela > 5) {
    $comentarios_estrela = 5;
}

$sucesso = salvarcomentarios($conexao, $coment_texto, $idUsuario, $comentarios_estrela);

if ($sucesso) {
    painelFlashDefinir('ok', 'Avaliação publicada com sucesso! Obrigado pelo seu feedback.');
} else {
    painelFlashDefinir('erro', 'Erro ao salvar avaliação. Tente novamente.');
}

header('Location: ../app/index.php?pagina=avalie');
exit;

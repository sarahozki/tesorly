<?php
session_start();
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../funcoes.php';

verificarLogin();

$idcomentarios = (int)($_POST['idcomentarios'] ?? $_POST['id'] ?? $_GET['id'] ?? 0);
$idUsuario = (int)($_SESSION['id'] ?? $_SESSION['id_usuario'] ?? 0);

if ($idcomentarios > 0 && $idUsuario > 0) {
    $deletado = deletarcomentarios($conexao, $idcomentarios, $idUsuario);
    if ($deletado) {
        painelFlashDefinir('ok', 'Comentário excluído com sucesso.');
    } else {
        painelFlashDefinir('erro', 'Erro ao excluir comentário ou você não tem permissão.');
    }
} else {
    painelFlashDefinir('erro', 'Identificador de comentário inválido.');
}

header('Location: ../app/index.php?pagina=avalie');
exit;

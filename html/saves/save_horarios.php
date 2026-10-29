<?php
// Recebe o formulário "Meus horários" e grava. Só empresário.
session_start();
require_once __DIR__ . '/../funcoes.php';

$app = '../app/index.php';
$voltar = $app . '?pagina=meus_horarios';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id'])) {
    header('Location: ' . $app);
    exit;
}

$idempresario = agendaIdEmpresarioDoUsuario($conexao, (int)$_SESSION['id']);
if ($idempresario === null) {
    header('Location: ' . $app); // cliente comum não tem acesso
    exit;
}

if (!csrfValido($_POST['csrf'] ?? null)) {
    painelFlashDefinir('erro', 'Sua sessão expirou. Tente salvar de novo.', $_POST);
    header('Location: ' . $voltar);
    exit;
}

$validacao = painelValidarHorarios($_POST);
if ($validacao['erro'] !== null) {
    painelFlashDefinir('erro', $validacao['erro'], $_POST); // mantém o que a pessoa digitou
    header('Location: ' . $voltar);
    exit;
}

try {
    painelSalvarHorarios($conexao, $idempresario, $validacao['turnos'], $validacao['rotulos']);
} catch (Throwable $erro) {
    painelFlashDefinir('erro', painelErroParaTela($erro, 'save_horarios'), $_POST);
    header('Location: ' . $voltar);
    exit;
}

if (!$validacao['turnos']) {
    painelFlashDefinir('aviso', 'Horários salvos, mas nenhum dia está marcado: os clientes não conseguem agendar com você.');
} else {
    painelFlashDefinir('ok', 'Horários de atendimento salvos!');
}

header('Location: ' . $voltar);
exit;

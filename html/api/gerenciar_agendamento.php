<?php
// POST (JSON) {"idagenda":5, "acao":"cancelar"}  ou  {"idagenda":5, "acao":"confirmar"}
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../funcoes.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false) {
    http_response_code(415);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
$idagenda = is_array($dados) ? (int)($dados['idagenda'] ?? 0) : 0;
$acao     = (is_array($dados) && is_string($dados['acao'] ?? null)) ? $dados['acao'] : '';

if (!$idagenda || $acao === '') {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

$resultado = agendaAlterarStatus($conexao, $idagenda, $acao, (int)$_SESSION['id']);

http_response_code($resultado['sucesso'] ? 200 : 400);
echo json_encode($resultado);

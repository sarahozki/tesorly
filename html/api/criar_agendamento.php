<?php
// POST (JSON) {"idempresario":1, "idservico":2, "data":"2026-11-03", "hora":"09:30"}
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../funcoes.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você precisa estar logado.']);
    exit;
}

// só aceita JSON: um formulário de outro site não consegue enviar isso (proteção simples contra CSRF)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false) {
    http_response_code(415);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida.']);
    exit;
}

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

$idempresario = (int)($dados['idempresario'] ?? 0);
$idservico    = (int)($dados['idservico'] ?? 0);
$data         = is_string($dados['data'] ?? null) ? $dados['data'] : '';
$hora         = is_string($dados['hora'] ?? null) ? $dados['hora'] : '';

if (!$idempresario || !$idservico || !agendaDataValida($data) || $hora === '') {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha profissional, serviço, data e horário.']);
    exit;
}

$resultado = agendaCriar($conexao, (int)$_SESSION['id'], $idempresario, $idservico, $data, $hora);

http_response_code($resultado['sucesso'] ? 200 : 409); // 409 = horário não está (mais) livre
echo json_encode($resultado);

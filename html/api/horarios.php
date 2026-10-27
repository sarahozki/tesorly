<?php
// GET ?idempresario=1&idservico=2&data=2026-11-03
// Devolve todos os horários do dia (de 30 em 30 min) marcando quais estão livres.
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../funcoes.php';

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Você precisa estar logado.']);
    exit;
}

$idempresario = (int)($_GET['idempresario'] ?? 0);
$idservico    = (int)($_GET['idservico'] ?? 0);
$data         = is_string($_GET['data'] ?? null) ? $_GET['data'] : '';

if (!$idempresario || !$idservico || !agendaDataValida($data)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Informe profissional, serviço e uma data válida.']);
    exit;
}

echo json_encode([
    'data'     => $data,
    'horarios' => agendaHorariosDoDia($conexao, $idempresario, $data, $idservico),
]);

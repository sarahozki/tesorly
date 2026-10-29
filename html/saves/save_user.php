<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

include '../conexao.php';
include '../funcoes.php';

// Se a foto passa do limite de envio do servidor (post_max_size), o PHP descarta TUDO: $_POST e $_FILES chegam vazios
if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    echo "<script>alert('O envio passou do limite do servidor (provavelmente a foto é grande demais). Use uma foto menor.'); window.history.back();</script>";
    exit;
}

$nome       = trim($_POST['nome'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telefone   = trim($_POST['telefone'] ?? '');
$senha_pura = $_POST['senha'] ?? '';
$sou_emp    = isset($_POST['sou_emp']);
$fotos      = $_FILES['prod_fotos'] ?? null;

// 1. Campos de texto
if ($nome === '' || $email === '' || $telefone === '' || $senha_pura === '') {
    echo "<script>alert('Preencha todos os campos.'); window.history.back();</script>";
    exit;
}

// 2. Email válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email inválido.'); window.history.back();</script>";
    exit;
}

// 3. Regras da senha (o JS do formulário pode ser contornado)
if (strlen($senha_pura) < 8 || !preg_match('/[0-9]/', $senha_pura) || !preg_match('/[A-Z]/', $senha_pura)) {
    echo "<script>alert('A senha precisa de 8+ caracteres, 1 número e 1 letra maiúscula.'); window.history.back();</script>";
    exit;
}

// 4. Foto (tipo real da imagem e tamanho) — antes de gravar qualquer coisa
$erro_foto = usuarioValidarFoto($fotos);
if ($erro_foto !== null) {
    echo "<script>alert(" . json_encode($erro_foto) . "); window.history.back();</script>";
    exit;
}

// 5. Salvar
$senha_criptografada = password_hash($senha_pura, PASSWORD_DEFAULT);

try {
    $id_usuario = salvaruser($conexao, $nome, $email, $senha_criptografada, $telefone, $fotos);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo "<script>alert('Este email já está cadastrado.'); window.history.back();</script>";
    } else {
        echo "Erro ao salvar no banco: " . htmlspecialchars($e->getMessage());
    }
    mysqli_close($conexao);
    exit;
}

// 6. Resultado
if (!$id_usuario) {
    echo "<script>alert('Não foi possível salvar a foto no servidor. Verifique a permissão de escrita da pasta uploads/usuarios.'); window.history.back();</script>";
    mysqli_close($conexao);
    exit;
}

$_SESSION['id_usuario'] = $id_usuario;
mysqli_close($conexao);

if ($sou_emp) {
    echo "<script>window.location.href='../empresarios/cad_emp.php';</script>";
} else {
    echo "<script>alert('Usuário cadastrado com sucesso!'); window.location.href='../index.php';</script>";
}
?>
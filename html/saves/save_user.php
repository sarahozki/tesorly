<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

include '../conexao.php';
include '../funcoes.php';

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

// 4. Upload da foto
if ($fotos === null || $fotos['error'] === UPLOAD_ERR_NO_FILE) {
    echo "<script>alert('Envie uma foto.'); window.history.back();</script>";
    exit;
}

if ($fotos['error'] !== UPLOAD_ERR_OK) {
    echo "<script>alert('Erro no upload da foto (código {$fotos['error']}). Talvez o arquivo seja grande demais.'); window.history.back();</script>";
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
    echo "<script>alert('Foto inválida. Use jpg, png ou webp.'); window.history.back();</script>";
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
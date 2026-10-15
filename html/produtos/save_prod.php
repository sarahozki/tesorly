<?php
session_start();
require_once ("../conexao.php");
require_once ("../funcoes.php");


$nome = $_POST['nome'];
$marca = $_POST['marca'];
$fotos = $_POST['fotos'];

$sql = "INSERT INTO produtos
(prod_nome, prod_marca, prod_fotos)
VALUES (?, ?, ?, ?)";

$comando = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $comando,
    "sss",
    $nome,
    $marca,
    $fotos
);

if (mysqli_stmt_execute($comando)) {
    echo "produto cadastrado com sucesso!";
} else {
    echo "Erro: " . mysqli_error($conexao);
}

mysqli_stmt_close($comando);
mysqli_close($conexao);

?>
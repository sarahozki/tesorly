<?php
session_start();
require_once ("../conexao.php");
require_once ("../funcoes.php");




echo "<h2> Produtos Cadastrados </h2>";
$produtos = listarprodutos($conexao);

foreach ($produtos as $produto) {
    print_r($produto);
    echo "<br>";
}

?>
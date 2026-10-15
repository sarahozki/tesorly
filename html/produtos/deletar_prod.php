<?php

session_start();
require_once ("../conexao.php");
require_once ("../funcoes.php");


echo "<h3>Deletar Produto</h3>";
$deletado = deletarprodutos($conexao, 2);
if($deletado){
    echo "Produto deletado com sucesso.";
}else{
    echo "Erro ao deletar produto. Verifique se o produto está cadastrado.";
}
    
?>
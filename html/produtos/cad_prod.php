<?php
session_start();
require_once '../funcoes.php';


if (isset($_POST['enviar'])) {
    // Obter e remover espaços em branco das extremidades
    $nome = $_POST['prod_nome'] ?? '';
    $marca = $_POST['prod_marca'] ?? '';
    $empresarioid = $_POST['empresario_idempresario']?? '';
    $fotos = $_FILES['prod_fotos'] ?? null;

    $nomeArquivo = uploadImg($_FILES['prod_fotos']);

if ($nomeArquivo === false) {
    echo "Erro no upload da imagem. Verifique o formato (jpg, png, webp) e o tamanho (até 5MB).";
} else 
    $sucesso = salvarprodutos($conexao, $nome, $marca, $nomeArquivo, $empresarioid);

    if ($sucesso) {
        echo "Produto cadastrado com sucesso!";
    } else {
        echo "Erro no cadastro do produto. Verifique a imagem ou a conexão.";
    }
    
}
?>

<form method="POST" enctype="multipart/form-data">
    <p>
        <label>Nome: </label><br>
        <input type="text" name="prod_nome" required>
    </p>
     
    <p>
        <label>Marca: </label><br>
        <input type="text" name="prod_marca" required> 
    </p>
    <p>
        <label>Selecione o empresário: </label><br>
        <input type="number" name="empresario_idempresario" required> 
    </p>
    <p>
        <label>Foto do Produto: </label><br>
        <input type="file" name="prod_fotos" accept="image/*" required>
    </p>
    <button type="submit" name="enviar">Enviar Imagem</button>
</form>

<br><br>


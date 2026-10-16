<?php
session_start();
require_once ("../conexao.php");
require_once '../funcoes.php';
verificarLogin();

echo "<h1> Editar Perfil </h1>";

if (isset($_POST['editar_perfil'])) {

    $id = $_POST['id']??'';
    $nome = $_POST['nome']??'';
    $email = $_POST['email']??'';
    $telefone = $_POST['telefone']??'';
    $senha = $_POST['senha']??'';

    $arquivoImagem = $_FILES['foto']?? null;

    if (atualizarUser(
        $conexao,
        $id,
        $nome,
        $email,
        $telefone,
        $senha,
        $arquivoImagem
    )) {
        echo "Perfil atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar o perfil.";
    }
}

$perfil = listarUser($conexao);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">

        <label for="perfil_id">Selecionar Perfil:</label><br>
            <select name="id" id="perfil_id" required>
                <option value="">-- Escolha um perfil --</option>
                <?php while ($perfil_info = $perfil->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($perfil_info['id']) ?>">
                        #<?php echo htmlspecialchars($perfil_info['id']) ?> - <?php echo htmlspecialchars($perfil_info['nome']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <br><br>
        <label>Nome:</label>
        <input type="text" name="nome"><br><br>
        <label>Email:</label>
        <input type="email" name="email"><br><br>
        <label>Telefone:</label>
        <input type="text" name="telefone"><br><br>
        <label>Senha:</label>
        <input type="password" name="senha"><br><br>
        <label>Foto:</label>
        <input type="file" name="foto"><br><br>
        <button type="submit" name="atualizar">Atualizar Perfil</button>
    </form>
    
</body>
</html>
?>
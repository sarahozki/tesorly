<?php

require_once '../funcoes.php';

verificarLogin();

$usuario = pesquisaruserid($conexao, $_SESSION['id']);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Meu Perfil - Tesorly</title>
    <style>

        /* depois passar esse css para a pagina de css e concertar para ficar centralizado e responsivo */
        .perfil_card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 32px;
            width: 320px;
            text-align: center;
        }

        .perfil_foto {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 16px;
            border: 3px solid #eee;
        }

        .perfil_nome {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .perfil_info {
            margin-top: 20px;
            text-align: left;
        }

        .perfil_info_item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .perfil_info_item:last-child {
            border-bottom: none;
        }

        .perfil_info_label {
            color: #888;
            font-weight: 500;
        }

        .perfil_info_valor {
            color: #222;
            font-weight: 600;
        }

        .perfil_button {
            margin-top: 24px;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: #222;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }

        .perfil_button:hover {
            background: #444;
        }
    </style>
</head>
<body class="perfil_body">

    <div class="perfil_card">
        <?php if (!empty($usuario['user_fotos'])): ?>
            <img src="../uploads/usuarios/<?= htmlspecialchars($usuario['user_fotos']) ?>" alt="Foto de perfil" class="perfil_foto">
        <?php else: ?>
            <img src="../uploads/usuarios" alt="Sem foto" class="perfil_foto">
        <?php endif; ?>

        <div class="perfil_nome"><?= htmlspecialchars($usuario['user_nome']) ?></div>

        <div class="perfil_info">
            <div class="perfil_info_item">
                <span class="perfil_info_label">Email</span>
                <span class="perfil_info_valor"><?= htmlspecialchars($usuario['user_email']) ?></span>
            </div>
            <div class="perfil_info_item">
                <span class="perfil_info_label">Telefone</span>
                <span class="perfil_info_valor"><?= htmlspecialchars($usuario['user_telefone']) ?></span>
            </div>
        </div>

        <button class="perfil_button" onclick="location.href='editar_perfil.php'" id="editar_perfil">Editar Perfil</button>
        <button class="perfil_button" onclick="location.href='../comentarios/cad_coment.php'">Comentar</button>
    </div>


</body>
</html>
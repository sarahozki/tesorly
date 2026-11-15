<?php
session_start();

require_once("../conexao.php");
require_once("../funcoes.php");

$mensagem = "";

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuarios_idusuario = $_SESSION['id_usuario'];
    $tipo = $_POST['empre_tipo'] ?? '';
    $dias_trab = $_POST['empre_dias_trab'] ?? '';
    $cidade = $_POST['empre_cidade'] ?? '';

    $resultado = salvarempresario($conexao, $usuarios_idusuario, $tipo, $dias_trab, $cidade);

    if ($resultado) {
        unset($_SESSION['id_usuario']);
        $mensagem = "Empresário cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar.";
    }
}

$mensagem = $mensagem ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <title>Sou Empresário - Tesorly</title>
    <style>
        .body-empresario {
            margin: 0;
            min-height: 100vh;
            background: #cfc4fa;
            font-family: Arial, Helvetica, sans-serif;
            color: #2a1a3a;
        }

        .empresario-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* ---------- lado esquerdo ---------- */
        .empresario-esquerda {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 24px;
        }

        .empresario-logo {
            height: 70px;
            object-fit: contain;
        }

        .empresario-card {
            width: 100%;
            max-width: 430px;
            padding: 24px 40px 32px;
            border: 2px solid #4a1d5c;
            border-radius: 40px;
            background: transparent;
            box-sizing: border-box;
        }

        .empresario-topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .empresario-equipe {
            margin: 0;
            font-weight: bold;
            color: #4a3fb0;
        }

        .empresario-voltar {
            font-size: 1.4rem;
            text-decoration: none;
            color: #2a1a3a;
        }

        .empresario-titulo {
            margin: 12px 0 18px;
            text-align: center;
            font-size: 1.7rem;
            font-weight: 500;
        }

        .empresario-form .form-content {
            display: flex;
            flex-direction: column;
            margin-bottom: 14px;
        }

        .empresario-form label {
            font-size: 0.7rem;
            margin-bottom: 3px;
        }

        .empresario-form input[type="text"] {
            height: 34px;
            padding: 0 10px;
            border: none;
            border-radius: 6px;
            background: #f1eefc;
            box-sizing: border-box;
        }

        .empresario-submit {
            width: 100%;
            height: 34px;
            margin-top: 6px;
            border: none;
            border-radius: 6px;
            background: #4a0a35;
            color: #fff;
            font-weight: bold;
            font-size: 0.75rem;
            cursor: pointer;
        }

        .empresario-submit:hover {
            background: #6b1450;
        }

        .mensagem {
            text-align: center;
            color: #b00020;
        }

        /* ---------- lado direito (foto) ---------- */
        .empresario-direita {
            overflow: hidden;
        }

        .empresario-foto {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-top-left-radius: 50% 100%;
            border-bottom-left-radius: 50% 100%;
        }

        /* ---------- celular ---------- */
        @media (max-width: 800px) {
            .empresario-layout {
                grid-template-columns: 1fr;
            }
            .empresario-direita {
                display: none;
            }
        }
    </style>
</head>
<body class="body-empresario">

    <main class="empresario-layout">

        <section class="empresario-esquerda">

            <div class="empresario-card">
                <div class="empresario-topo">
                    <a href="../index.php" class="empresario-voltar" aria-label="Voltar">◀</a>
                </div>

                <h1 class="empresario-titulo">Cadastro de Empresário</h1>

                <?php if ($mensagem): ?>
                    <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
                <?php endif; ?>

                <form action="" method="post" class="empresario-form">

                    <div class="form-content">
                        <label for="empre_usuario">Nome de usuário</label>
                        <input type="text" id="empre_usuario" name="empre_usuario" required>
                    </div>

                    <div class="form-content">
                        <label for="empre_tipo">Tipo de salão</label>
                        <input type="text" id="empre_tipo" name="empre_tipo" required>
                    </div>

                    <div class="form-content">
                        <label for="empre_cidade">Cidade</label>
                        <input type="text" id="empre_cidade" name="empre_cidade" required>
                    </div>

                    <div class="form-content">
                        <label for="empre_dias_trab">Dias de trabalho</label>
                        <input type="text" id="empre_dias_trab" name="empre_dias_trab" required>
                    </div>

                    <div class="form-content">
                        <label for="empre_horarios">Horários semanais</label>
                        <input type="text" id="empre_horarios" name="empre_horarios" required>
                    </div>

                    <input type="submit" value="Finalizar Cadastro" id="cadastro" class="empresario-submit">
                </form>
            </div>
        </section>

        <section class="empresario-direita">
            <img src="../imagens/salao.jpg" alt="Foto do salão" class="empresario-foto">
        </section>

    </main>
</body>
</html>
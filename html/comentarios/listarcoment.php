<?php
session_start();
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../funcoes.php';

// Se estiver logado, redireciona para a página integrada completa
if (isset($_SESSION['id'])) {
    header('Location: ../app/index.php?pagina=avalie');
    exit;
}

$comentarios = listarcomentarios($conexao);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Comentários Recentes - Tesorly</title>
    <style>
        body {
            background: #ECE7F6;
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 30px 20px;
        }
        .container_comentarios {
            max-width: 800px;
            margin: 0 auto;
        }
        .topo_header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .topo_header h1 {
            color: #6B2F5C;
            font-size: 1.8rem;
        }
        .btn_voltar {
            background: #6B2F5C;
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }
        .feed_comentarios {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .card_comentario {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 20px;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(74, 45, 92, 0.08);
        }
        .avatar_comentario {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6B2F5C;
        }
        .conteudo_comentario {
            flex: 1;
        }
        .autor_comentario {
            font-weight: 700;
            color: #2E2340;
            font-size: 1.05rem;
        }
        .estrelas {
            color: #F5B82A;
            font-size: 1.1rem;
            letter-spacing: 2px;
            margin: 4px 0;
        }
        .texto_comentario {
            color: #4B5563;
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container_comentarios">
        <div class="topo_header">
            <h1>Avaliações e Comentários</h1>
            <a href="../index.php" class="btn_voltar">← Início</a>
        </div>

        <div class="feed_comentarios">
            <?php if (empty($comentarios)): ?>
                <div class="card_comentario">
                    <p>Nenhuma avaliação cadastrada ainda.</p>
                </div>
            <?php else: ?>
                <?php foreach ($comentarios as $c): ?>
                    <?php
                        $foto = !empty($c['user_fotos']) ? '../uploads/usuarios/' . htmlspecialchars($c['user_fotos']) : '../imagens/fotouser.png';
                        $estrelas = max(1, min(5, (int)$c['comentarios_estrela']));
                    ?>
                    <article class="card_comentario">
                        <img src="<?= $foto ?>" alt="Foto" class="avatar_comentario" onerror="this.onerror=null;this.src='../imagens/fotouser.png';">
                        <div class="conteudo_comentario">
                            <span class="autor_comentario"><?= htmlspecialchars($c['user_nome'] ?? 'Cliente') ?></span>
                            <div class="estrelas"><?= str_repeat('★', $estrelas) . str_repeat('☆', 5 - $estrelas) ?></div>
                            <p class="texto_comentario"><?= nl2br(htmlspecialchars($c['coment_texto'] ?? '')) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
session_start();

require_once("../conexao.php");
require_once("../funcoes.php");

$mensagem = "";

if (!isset($_SESSION['id_usuario']) && !isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit;
}

$usuarios_idusuario = (int)($_SESSION['id_usuario'] ?? $_SESSION['id']);
$usuario = pesquisaruserid($conexao, $usuarios_idusuario);
$nomeUsuario = $usuario['user_nome'] ?? '';
$nichosDisponiveis = agendaListarNichos();
$nichoEscolhido = 'cabeleireiro';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nichoEscolhido = agendaNormalizarNicho($_POST['empre_nicho'] ?? 'cabeleireiro');
    $tipo = trim($_POST['empre_tipo'] ?? '');
    if ($tipo === '') {
        $tipo = $nichosDisponiveis[$nichoEscolhido]['nome'] ?? 'Cabeleireiro';
    }
    $cidade = trim($_POST['empre_cidade'] ?? '');

    if ($cidade === '') {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
    } else {
        $resultado = salvarempresario($conexao, $usuarios_idusuario, $tipo, $cidade, 'A definir', $nichoEscolhido);

        if ($resultado) {
            if ($usuario) {
                $_SESSION['usuario'] = $usuario['user_nome'];
                $_SESSION['id'] = $usuario['idusuarios'];
                unset($_SESSION['id_usuario']);
                echo "<script>alert('Cadastro de profissional realizado com sucesso!'); window.location.href='../app/index.php?pagina=meus_servicos';</script>";
                exit;
            } else {
                unset($_SESSION['id_usuario']);
                echo "<script>alert('Cadastro de profissional realizado com sucesso! Faça login para continuar.'); window.location.href='../login.php';</script>";
                exit;
            }
        } else {
            $mensagem = "Erro ao cadastrar profissional. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Cadastro de Profissional - Tesorly</title>
    <style>
        * {
            box-sizing: border-box;
        }

        .body-empresario {
            margin: 0;
            min-height: 100vh;
            background: #ECE7F6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e1e24;
            -webkit-font-smoothing: antialiased;
        }

        .empresario-layout {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            min-height: 100vh;
        }

        /* ---------- lado esquerdo ---------- */
        .empresario-esquerda {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .empresario-card {
            width: 100%;
            max-width: 520px;
            padding: 36px 40px;
            border: 1px solid #dcd3ea;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .empresario-topo {
            margin-bottom: 20px;
        }

        .empresario-voltar {
            font-size: 0.88rem;
            text-decoration: none;
            color: #554477;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.15s ease;
        }

        .empresario-voltar:hover {
            color: #2b1f40;
        }

        .empresario-titulo {
            margin: 0 0 6px;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e1e24;
            letter-spacing: -0.02em;
        }

        .empresario-subtitulo {
            font-size: 0.9rem;
            color: #64748b;
            margin: 0 0 24px;
            line-height: 1.4;
        }

        .empresario-form .form-content {
            display: flex;
            flex-direction: column;
            margin-bottom: 18px;
        }

        .empresario-form label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }

        .empresario-form input[type="text"] {
            height: 42px;
            padding: 0 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            font-size: 0.92rem;
            color: #0f172a;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .empresario-form input[type="text"]:focus {
            border-color: #6B2F5C;
            box-shadow: 0 0 0 3px rgba(107, 47, 92, 0.12);
        }

        /* ---------- Seletor de segmento ---------- */
        .nichos-label-wrap {
            margin-bottom: 6px;
        }

        .nichos-label-wrap label {
            margin-bottom: 2px;
        }

        .nichos-ajuda {
            font-size: 0.78rem;
            color: #64748b;
            margin: 0 0 10px;
        }

        .nichos-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .nicho-card {
            position: relative;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 14px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 4px;
            transition: border-color 0.15s ease, background-color 0.15s ease;
            user-select: none;
        }

        .nicho-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .nicho-card:hover {
            border-color: #94a3b8;
            background: #fafafc;
        }

        .nicho-card-topo {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .nicho-titulo {
            font-size: 0.92rem;
            font-weight: 600;
            color: #0f172a;
        }

        .nicho-radio-indicator {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1.5px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.15s ease;
            flex-shrink: 0;
        }

        .nicho-radio-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: transparent;
            transition: background 0.15s ease;
        }

        .nicho-desc {
            font-size: 0.76rem;
            color: #64748b;
            line-height: 1.35;
        }

        /* Estado selecionado profissional */
        .nicho-card.selecionado {
            border-color: #6B2F5C;
            background: #FAF7FC;
        }

        .nicho-card.selecionado .nicho-titulo {
            color: #4A1D5C;
        }

        .nicho-card.selecionado .nicho-radio-indicator {
            border-color: #6B2F5C;
        }

        .nicho-card.selecionado .nicho-radio-dot {
            background: #6B2F5C;
        }

        /* Estilo selecionado barbearia */
        .nicho-card[data-nicho="barbearia"].selecionado {
            border-color: #262930;
            background: #18191D;
        }

        .nicho-card[data-nicho="barbearia"].selecionado .nicho-titulo {
            color: #E2E8F0;
        }

        .nicho-card[data-nicho="barbearia"].selecionado .nicho-desc {
            color: #94A3B8;
        }

        .nicho-card[data-nicho="barbearia"].selecionado .nicho-radio-indicator {
            border-color: #C5A059;
        }

        .nicho-card[data-nicho="barbearia"].selecionado .nicho-radio-dot {
            background: #C5A059;
        }

        .aviso_horarios_info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #475569;
            margin: 4px 0 18px;
            line-height: 1.45;
        }

        .empresario-submit {
            width: 100%;
            height: 44px;
            border: none;
            border-radius: 8px;
            background: #6B2F5C;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.94rem;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .empresario-submit:hover {
            background: #542247;
        }

        .mensagem {
            text-align: left;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 0.84rem;
            font-weight: 500;
            margin-bottom: 16px;
        }

        /* ---------- lado direito (foto) ---------- */
        .empresario-direita {
            overflow: hidden;
            background: #1e1526;
        }

        .empresario-foto {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.95;
        }

        /* ---------- responsivo ---------- */
        @media (max-width: 860px) {
            .empresario-layout {
                grid-template-columns: 1fr;
            }
            .empresario-direita {
                display: none;
            }
            .nichos-grid {
                grid-template-columns: 1fr;
            }
            .empresario-card {
                padding: 28px 20px;
            }
        }
    </style>
</head>
<body class="body-empresario">

    <main class="empresario-layout">

        <section class="empresario-esquerda">

            <div class="empresario-card">
                <div class="empresario-topo">
                    <a href="../index.php" class="empresario-voltar" aria-label="Voltar">← Voltar</a>
                </div>

                <h1 class="empresario-titulo">Cadastro profissional</h1>
                <p class="empresario-subtitulo">Configure seu perfil de atendimento no Tesorly</p>

                <?php if ($mensagem): ?>
                    <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
                <?php endif; ?>

                <form action="" method="post" class="empresario-form">

                    <?php if (!empty($nomeUsuario)): ?>
                        <div class="form-content">
                            <label for="empre_usuario">Profissional responsável</label>
                            <input type="text" id="empre_usuario" name="empre_usuario" value="<?= htmlspecialchars($nomeUsuario) ?>" readonly style="background: #f1f5f9; color: #475569; font-weight: 500; cursor: not-allowed;">
                        </div>
                    <?php endif; ?>

                    <div class="form-content">
                        <div class="nichos-label-wrap">
                            <label>Segmento de atuação *</label>
                            <p class="nichos-ajuda">Selecione a categoria principal do seu atendimento.</p>
                        </div>

                        <div class="nichos-grid">
                            <?php foreach ($nichosDisponiveis as $slug => $info): ?>
                                <?php $ativo = ($slug === $nichoEscolhido); ?>
                                <label class="nicho-card <?= $ativo ? 'selecionado' : '' ?>" data-nicho="<?= $slug ?>">
                                    <input type="radio" name="empre_nicho" value="<?= $slug ?>" <?= $ativo ? 'checked' : '' ?> required>
                                    <div class="nicho-card-topo">
                                        <span class="nicho-titulo"><?= htmlspecialchars($info['nome']) ?></span>
                                        <span class="nicho-radio-indicator">
                                            <span class="nicho-radio-dot"></span>
                                        </span>
                                    </div>
                                    <span class="nicho-desc"><?= htmlspecialchars($info['descricao']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <input type="hidden" id="empre_tipo" name="empre_tipo" value="<?= htmlspecialchars($nichosDisponiveis[$nichoEscolhido]['nome'] ?? 'Cabeleireiro') ?>">
                    </div>

                    <div class="form-content">
                        <label for="empre_cidade">Cidade de atendimento *</label>
                        <input type="text" id="empre_cidade" name="empre_cidade" placeholder="Ex.: São Paulo" required>
                    </div>

                    <div class="aviso_horarios_info">
                        Os horários e turnos de atendimento serão definidos na gestão da sua agenda após o cadastro.
                    </div>

                    <input type="submit" value="Concluir cadastro" id="cadastro" class="empresario-submit">
                </form>
            </div>
        </section>

        <section class="empresario-direita">
            <img src="../imagens/salao.jpg" alt="Foto do salão" class="empresario-foto" id="foto-salao-preview">
        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.nicho-card');
            const tipoInput = document.getElementById('empre_tipo');
            const nomesMap = {
                'cabeleireiro': 'Cabeleireiro',
                'barbearia': 'Barbearia',
                'maquiagem': 'Salão de Maquiagem',
                'naildesigner': 'Nail Designer'
            };

            cards.forEach(card => {
                card.addEventListener('click', function () {
                    cards.forEach(c => c.classList.remove('selecionado'));
                    this.classList.add('selecionado');

                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        const nicho = radio.value;
                        if (tipoInput && nomesMap[nicho]) {
                            tipoInput.value = nomesMap[nicho];
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

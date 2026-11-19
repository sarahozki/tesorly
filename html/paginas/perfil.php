<?php
// Meu Perfil - Tesorly
require_once RAIZ . '/funcoes.php';

verificarLogin();

$idUsuario = (int)$_SESSION['id'];
$usuario = pesquisaruserid($conexao, $idUsuario);

if (!$usuario) {
    echo '<p class="aviso_agenda">Usuário não encontrado. Faça <a href="../login.php">login novamente</a>.</p>';
    return;
}

// Verifica se é empresário e pega informações adicionais se houver
$idEmpresario = agendaIdEmpresarioDoUsuario($conexao, $idUsuario);
$ehEmpresario = ($idEmpresario !== null);

$dadosEmpresario = null;
if ($ehEmpresario) {
    $dadosEmpresario = agendaBuscarProfissional($conexao, $idEmpresario);
}

// Resolução da foto de perfil
$fotoUrl = '../imagens/fotouser.png';
if (!empty($usuario['user_fotos'])) {
    $nomeArquivo = basename(trim($usuario['user_fotos']));
    if (file_exists(RAIZ . '/uploads/usuarios/' . $nomeArquivo)) {
        $fotoUrl = '../uploads/usuarios/' . $nomeArquivo;
    }
}
?>

<style>
/* =========================================================
   ESTILOS DO PERFIL - PADRÃO TESORLY
   ========================================================= */

.perfil_layout_tesorly {
    display: flex;
    flex-direction: column;
    gap: 24px;
    max-width: 820px;
    margin: 0 auto;
}

/* Card Principal do Perfil */
.card_perfil_destaque {
    background: #ffffff;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 6px 20px rgba(74, 45, 92, 0.1);
    display: flex;
    align-items: center;
    gap: 28px;
    position: relative;
    overflow: hidden;
    border: 1px solid #ECE7F6;
}

.card_perfil_destaque::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 8px;
    background: linear-gradient(90deg, #6B2F5C 0%, #D6336C 50%, #F5B82A 100%);
}

/* Avatar com borda decorativa */
.perfil_avatar_wrapper {
    position: relative;
    flex-shrink: 0;
}

.perfil_avatar_img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #ffffff;
    box-shadow: 0 4px 16px rgba(107, 47, 92, 0.22);
    display: block;
    background: #ECE7F6;
}

.perfil_badge_online {
    position: absolute;
    bottom: 6px;
    right: 6px;
    width: 18px;
    height: 18px;
    background: #27AE60;
    border: 3px solid #ffffff;
    border-radius: 50%;
}

/* Informações do cabeçalho */
.perfil_header_info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.perfil_nome_usuario {
    font-size: 1.65rem;
    font-weight: 800;
    color: #2E2340;
    line-height: 1.2;
}

.perfil_tags_linha {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 4px;
}

.badge_perfil_tipo {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge_tipo_cliente {
    background: #F4E9F2;
    color: #6B2F5C;
    border: 1px solid #E2D9EE;
}

.badge_tipo_prof {
    background: #FDEDF3;
    color: #D6336C;
    border: 1px solid #F5C6CB;
}

.badge_status_ativo {
    background: #EBF8F1;
    color: #1E8449;
    border: 1px solid #C3E6CB;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 999px;
}

/* Grid de Detalhes da Conta */
.perfil_grid_dados {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.card_dado_item {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #ECE7F6;
    box-shadow: 0 4px 14px rgba(74, 45, 92, 0.06);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card_dado_item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(74, 45, 92, 0.1);
}

.icone_dado_box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #FAF7FD;
    border: 1px solid #E2D9EE;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #6B2F5C;
    flex-shrink: 0;
}

.texto_dado_box {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.label_dado {
    font-size: 0.78rem;
    color: #8A7E9C;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.valor_dado {
    font-size: 0.98rem;
    color: #2E2340;
    font-weight: 700;
    word-break: break-word;
}

/* Barra de Ações Rápidas */
.card_acoes_perfil {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #ECE7F6;
    box-shadow: 0 4px 14px rgba(74, 45, 92, 0.06);
}

.titulo_acoes_perfil {
    font-size: 1.05rem;
    color: #2E2340;
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.grupo_botoes_perfil {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.btn_perfil_acao {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 0.92rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
}

.btn_perfil_primario {
    background: #D6336C;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(214, 51, 108, 0.25);
}

.btn_perfil_primario:hover {
    background: #B82A5B;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(214, 51, 108, 0.35);
}

.btn_perfil_secundario {
    background: #FAF7FD;
    color: #6B2F5C;
    border: 1.5px solid #6B2F5C;
}

.btn_perfil_secundario:hover {
    background: #F4E9F2;
    transform: translateY(-2px);
}

.btn_perfil_perigo {
    background: #FDEDF3;
    color: #C0392B;
    border: 1.5px solid #F5C6CB;
    margin-left: auto;
}

.btn_perfil_perigo:hover {
    background: #F9DADA;
    transform: translateY(-2px);
}

@media (max-width: 720px) {
    .card_perfil_destaque {
        flex-direction: column;
        text-align: center;
        padding: 24px 20px;
    }
    .perfil_tags_linha {
        justify-content: center;
    }
    .perfil_grid_dados {
        grid-template-columns: 1fr;
    }
    .btn_perfil_perigo {
        margin-left: 0;
        width: 100%;
        justify-content: center;
    }
    .btn_perfil_acao {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="perfil_layout_tesorly">
    <div>
        <h2 class="titulo_pagina_agenda">Meu Perfil</h2>
        <p class="sub_agenda">Gerencie suas informações pessoais, contatos e preferências na plataforma Tesorly.</p>
    </div>

    <!-- Card Destaque do Usuário -->
    <div class="card_perfil_destaque">
        <div class="perfil_avatar_wrapper">
            <img src="<?= htmlspecialchars($fotoUrl) ?>" alt="Foto de <?= htmlspecialchars($usuario['user_nome']) ?>" class="perfil_avatar_img" onerror="this.onerror=null;this.src='../imagens/fotouser.png';">
            <span class="perfil_badge_online" title="Conta ativa"></span>
        </div>

        <div class="perfil_header_info">
            <h1 class="perfil_nome_usuario"><?= htmlspecialchars($usuario['user_nome']) ?></h1>
            <div class="perfil_tags_linha">
                <?php if ($ehEmpresario): ?>
                    <span class="badge_perfil_tipo badge_tipo_prof">✿ Profissional / Salão</span>
                <?php else: ?>
                    <span class="badge_perfil_tipo badge_tipo_cliente">✿ Cliente</span>
                <?php endif; ?>
                <span class="badge_status_ativo">✓ Conta Verificada</span>
            </div>
        </div>
    </div>

    <!-- Grid de Informações Detalhadas -->
    <div class="perfil_grid_dados">
        <!-- E-mail -->
        <div class="card_dado_item">
            <div class="icone_dado_box">✉</div>
            <div class="texto_dado_box">
                <span class="label_dado">E-mail Cadastrado</span>
                <span class="valor_dado"><?= htmlspecialchars($usuario['user_email']) ?></span>
            </div>
        </div>

        <!-- Telefone -->
        <div class="card_dado_item">
            <div class="icone_dado_box">📞</div>
            <div class="texto_dado_box">
                <span class="label_dado">Telefone / WhatsApp</span>
                <span class="valor_dado"><?= htmlspecialchars($usuario['user_telefone'] ?: 'Não informado') ?></span>
            </div>
        </div>

        <?php if ($ehEmpresario && $dadosEmpresario): ?>
            <!-- Especialidade -->
            <div class="card_dado_item">
                <div class="icone_dado_box">✂️</div>
                <div class="texto_dado_box">
                    <span class="label_dado">Especialidade / Ramo</span>
                    <span class="valor_dado"><?= htmlspecialchars($dadosEmpresario['empre_tipo'] ?? 'Salão de Beleza') ?></span>
                </div>
            </div>

            <!-- Cidade -->
            <div class="card_dado_item">
                <div class="icone_dado_box">📍</div>
                <div class="texto_dado_box">
                    <span class="label_dado">Cidade de Atendimento</span>
                    <span class="valor_dado"><?= htmlspecialchars($dadosEmpresario['empre_cidade'] ?? 'Não informada') ?></span>
                </div>
            </div>
        <?php else: ?>
            <!-- Segurança -->
            <div class="card_dado_item">
                <div class="icone_dado_box">🔒</div>
                <div class="texto_dado_box">
                    <span class="label_dado">Segurança da Conta</span>
                    <span class="valor_dado">Senha criptografada</span>
                </div>
            </div>

            <!-- Atividade -->
            <div class="card_dado_item">
                <div class="icone_dado_box">⭐</div>
                <div class="texto_dado_box">
                    <span class="label_dado">Participação</span>
                    <span class="valor_dado">Membro Tesorly</span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Card de Ações Rápidas -->
    <div class="card_acoes_perfil">
        <h3 class="titulo_acoes_perfil">⚡ Ações e Configurações</h3>
        <div class="grupo_botoes_perfil">
            <a href="?pagina=editar_perfil" class="btn_perfil_acao btn_perfil_primario" id="btn_editar_perfil">
                ✏️ Editar Meus Dados
            </a>

            <a href="?pagina=avalie" class="btn_perfil_acao btn_perfil_secundario">
                ⭐ Avaliar e Ver Comentários
            </a>

            <?php if ($ehEmpresario): ?>
                <a href="?pagina=meus_servicos" class="btn_perfil_acao btn_perfil_secundario">
                    💼 Gerenciar Serviços
                </a>
            <?php endif; ?>

            <a href="../deslogar.php" class="btn_perfil_acao btn_perfil_perigo" onclick="return confirm('Deseja realmente sair da sua conta?');">
                🚪 Sair da Conta
            </a>
        </div>
    </div>
</div>

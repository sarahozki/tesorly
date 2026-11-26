<?php
// Página de Avaliações e Comentários do Tesorly
require_once RAIZ . '/funcoes.php';

if (!isset($_SESSION['id'])) {
    echo '<p class="aviso_agenda">Faça <a href="../login.php">login</a> para avaliar.</p>';
    return;
}

$idUsuarioLogado = (int)$_SESSION['id'];

// ---------- Processamento de ações (POST) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if (!csrfValido($_POST['csrf'] ?? '')) {
        painelFlashDefinir('erro', 'Sessão expirada. Por favor, tente novamente.');
        header('Location: ?pagina=avalie');
        exit;
    }

    if ($acao === 'criar') {
        $coment_texto = trim($_POST['coment_texto'] ?? '');
        $comentarios_estrela = (int)($_POST['comentarios_estrela'] ?? 5);

        if ($coment_texto === '') {
            painelFlashDefinir('erro', 'Por favor, escreva uma mensagem para sua avaliação.', [
                'texto' => $coment_texto,
                'estrela' => $comentarios_estrela
            ]);
            header('Location: ?pagina=avalie');
            exit;
        }

        if (mb_strlen($coment_texto) > 255) {
            painelFlashDefinir('erro', 'A avaliação não pode ultrapassar 255 caracteres.', [
                'texto' => $coment_texto,
                'estrela' => $comentarios_estrela
            ]);
            header('Location: ?pagina=avalie');
            exit;
        }

        if ($comentarios_estrela < 1 || $comentarios_estrela > 5) {
            $comentarios_estrela = 5;
        }

        $salvou = salvarcomentarios($conexao, $coment_texto, $idUsuarioLogado, $comentarios_estrela);

        if ($salvou) {
            painelFlashDefinir('ok', 'Avaliação publicada com sucesso! Obrigado pelo seu feedback.');
        } else {
            painelFlashDefinir('erro', 'Não foi possível salvar o comentário no momento. Tente novamente.');
        }

        header('Location: ?pagina=avalie');
        exit;
    }

    if ($acao === 'excluir') {
        $idcomentarios = (int)($_POST['idcomentarios'] ?? 0);

        if ($idcomentarios > 0 && deletarcomentarios($conexao, $idcomentarios, $idUsuarioLogado)) {
            painelFlashDefinir('ok', 'Avaliação excluída com sucesso.');
        } else {
            painelFlashDefinir('erro', 'Erro ao excluir a avaliação ou permissão negada.');
        }

        header('Location: ?pagina=avalie');
        exit;
    }
}

// ---------- Carregamento dos dados ----------
$flash = painelFlashLer();
$dadosFlash = $flash['dados'] ?? [];
$vTexto = $dadosFlash['texto'] ?? '';
$vEstrela = (int)($dadosFlash['estrela'] ?? 5);

$stats = obterEstatisticasComentarios($conexao);
$comentarios = listarcomentarios($conexao);

$rotulosEstrelas = [
    5 => 'Excelente',
    4 => 'Muito bom',
    3 => 'Bom',
    2 => 'Regular',
    1 => 'Ruim',
];
?>

<!-- Biblioteca Chart.js para representação gráfica -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* =========================================================
   ESTILOS EXCLUSIVOS: AVALIAÇÃO COM ESTRELAS PILL & COMENTÁRIOS
   Garante aplicação imediata contra cache de navegador
   ========================================================= */

.avalie_grid_topo {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 22px;
    margin-bottom: 28px;
}

@media (max-width: 900px) {
    .avalie_grid_topo {
        grid-template-columns: 1fr;
    }
}

/* Card de estatísticas */
.card_stats_avalie {
    background: var(--cor-card, #ffffff);
    border: 1px solid var(--cor-borda-card, transparent);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--sombra-card, 0 6px 18px rgba(74, 45, 92, 0.1));
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.stats_score_container {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 18px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--cor-bg, #ECE7F6);
}

.stats_score_grande {
    font-size: 3.4rem;
    font-weight: 800;
    color: var(--cor-destaque, #6B2F5C);
    line-height: 1;
}

.stats_estrelas_wrap {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.stats_estrelas_pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #FAF7FD;
    border: 1px solid #E2D9EE;
    border-radius: 999px;
    padding: 6px 12px;
}

.stats_total_txt {
    font-size: 0.84rem;
    color: #6b6280;
    font-weight: 500;
}

/* Linhas com cápsulas de estrelas (estilo da imagem de referência) */
.stats_pills_container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 18px;
}

.stats_pill_linha {
    display: flex;
    align-items: center;
    gap: 12px;
}

.estrela_pill_mini {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #FFFFFF;
    border: 1.5px solid #E2D9EE;
    border-radius: 999px;
    padding: 4px 10px;
    box-shadow: 0 2px 6px rgba(74, 45, 92, 0.05);
    flex-shrink: 0;
}

.estrela_svg_mini {
    width: 17px;
    height: 17px;
    display: block;
}

.estrela_svg_mini.amarela {
    fill: #F5B82A !important;
}

.estrela_svg_mini.cinza {
    fill: #CBD5E1 !important;
}

.barra_estrela_track {
    flex: 1;
    height: 10px;
    background: #ECE7F6;
    border-radius: 999px;
    overflow: hidden;
}

.barra_estrela_fill {
    height: 100%;
    background: #F5B82A;
    border-radius: 999px;
    transition: width 0.4s ease;
}

.barra_estrela_qtd {
    width: 65px;
    font-size: 0.78rem;
    color: #6B2F5C;
    font-weight: 600;
    text-align: right;
    white-space: nowrap;
}

/* Gráfico Chart.js */
.grafico_container_avalie {
    padding-top: 16px;
    border-top: 1px solid #ECE7F6;
}

.grafico_header_tabs {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.grafico_titulo {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--cor-subtitulo, #6B2F5C);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.grafico_abas_btn {
    display: flex;
    gap: 6px;
}

.btn_tab_grafico {
    background: var(--cor-bg, #ECE7F6);
    border: none;
    color: var(--cor-subtitulo, #6B2F5C);
    font-size: 0.75rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn_tab_grafico.ativo,
.btn_tab_grafico:hover {
    background: var(--cor-destaque, #6B2F5C);
    color: var(--cor-destaque-texto, #ffffff);
}

.canvas_chart_wrap {
    position: relative;
    width: 100%;
    height: 170px;
}

/* Card do formulário */
.card_form_avalie {
    background: var(--cor-card, #ffffff);
    border: 1px solid var(--cor-borda-card, transparent);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 6px 18px rgba(74, 45, 92, 0.1);
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* SELETOR DE ESTRELAS PILL (CONFORME A IMAGEM DE REFERÊNCIA) */
.estrela_pill_wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
}

.estrela_pill {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #FFFFFF;
    border: 2px solid #E2D9EE;
    border-radius: 16px;
    padding: 10px 18px;
    box-shadow: 0 4px 14px rgba(74, 45, 92, 0.07);
    transition: all 0.2s ease;
    cursor: pointer;
}

.estrela_pill:hover {
    border-color: #F5B82A;
    box-shadow: 0 6px 18px rgba(245, 184, 42, 0.22);
}

.estrela_btn_pill {
    background: transparent;
    border: none;
    outline: none;
    padding: 2px;
    margin: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.18s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.estrela_btn_pill:hover {
    transform: scale(1.28);
}

.estrela_btn_pill svg {
    width: 36px;
    height: 36px;
    fill: #CBD5E1; /* Inativa (cinza suave) */
    transition: fill 0.15s ease, filter 0.15s ease;
}

/* Estrela ativa / preenchida (amarelo ouro vibrante) */
.estrela_btn_pill.amarela svg {
    fill: #F5B82A !important;
    filter: drop-shadow(0 2px 5px rgba(245, 184, 42, 0.5));
}

.estrela_btn_pill.animar_click svg {
    animation: estrelaPop 0.28s ease-out;
}

@keyframes estrelaPop {
    0% { transform: scale(1); }
    50% { transform: scale(1.4); }
    100% { transform: scale(1); }
}

.rating_texto_legenda {
    font-size: 0.9rem;
    font-weight: 700;
    color: #6B2F5C;
    min-height: 1.3em;
}

/* =========================================================
   LISTAGEM DE COMENTÁRIOS RECENTES COM ESTILO COMPLETO
   ========================================================= */

.secao_comentarios_titulo {
    color: #2E2340;
    font-size: 1.35rem;
    margin: 36px 0 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.feed_comentarios {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 30px;
}

.card_comentario {
    display: flex;
    gap: 18px;
    align-items: flex-start;
    padding: 22px;
    border-radius: 16px;
    background: var(--cor-card, #FFFFFF);
    border: 1px solid var(--cor-borda-card, #ECE7F6);
    box-shadow: var(--sombra-card, 0 4px 16px rgba(74, 45, 92, 0.08));
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card_comentario:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(74, 45, 92, 0.12);
}

.avatar_comentario_wrapper {
    position: relative;
    flex-shrink: 0;
}

.avatar_comentario {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--cor-destaque, #6B2F5C);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    background: var(--cor-bg, #ECE7F6);
    display: block;
}

.conteudo_comentario {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.topo_comentario {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.autor_comentario_box {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.autor_comentario {
    font-weight: 700;
    color: var(--cor-card-titulo, #2E2340);
    font-size: 1.05rem;
}

.badge_meu_comentario {
    background: var(--cor-badge-bg, #FDEDF3);
    color: var(--cor-destaque, #D6336C);
    border: 1px solid var(--cor-borda-card, #F5C6CB);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 2px 10px;
    border-radius: 999px;
}

.badge_verificado {
    background: #EBF8F1;
    color: #1E8449;
    border: 1px solid #C3E6CB;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 999px;
}

/* Pílula de estrelas dentro do comentário */
.pill_estrelas_comentario {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: var(--cor-bg, #FAF7FD);
    border: 1px solid var(--cor-input-borda, #E2D9EE);
    border-radius: 999px;
    padding: 4px 10px;
}

.texto_comentario {
    color: var(--cor-card-texto, #374151);
    font-size: 0.95rem;
    line-height: 1.6;
    word-break: break-word;
    background: var(--cor-input-bg, #FDFBFF);
    padding: 10px 14px;
    border-radius: 10px;
    border-left: 3px solid var(--cor-destaque, #6B2F5C);
}

.rodape_comentario {
    display: flex;
    justify-content: flex-end;
    margin-top: 4px;
}

.btn_deletar_comentario {
    background: #FDEDF3;
    border: 1px solid #F5C6CB;
    color: #C0392B;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    padding: 6px 12px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.btn_deletar_comentario:hover {
    background: #F9DADA;
    color: #922B21;
    transform: translateY(-1px);
}
</style>

<h2 class="titulo_pagina_agenda">Avaliações e Comentários</h2>
<p class="sub_agenda">Compartilhe sua experiência no Tesorly e veja a opinião de quem já usou a plataforma.</p>

<?php if ($flash): ?>
    <p class="flash_painel flash_<?= htmlspecialchars($flash['tipo']) ?>" role="status">
        <?= htmlspecialchars($flash['texto']) ?>
    </p>
<?php endif; ?>

<div class="avalie_grid_topo">
    <!-- Placar de estatísticas com cápsulas de estrelas e gráfico -->
    <div class="card_stats_avalie">
        <h3 class="subtitulo_painel">Classificação Geral</h3>

        <div class="stats_score_container">
            <div class="stats_score_grande">
                <?= number_format($stats['media'], 1, ',', '.') ?>
            </div>
            <div class="stats_estrelas_wrap">
                <div class="stats_estrelas_pill">
                    <?php
                        $mediaArredondada = (int)round($stats['media']);
                        for ($s = 1; $s <= 5; $s++):
                            $corEstrela = ($s <= $mediaArredondada) ? 'amarela' : 'cinza';
                    ?>
                        <svg class="estrela_svg_mini <?= $corEstrela ?>" viewBox="0 0 24 24">
                            <path d="M12 1.7l2.85 6.32 6.89.61c.6.05.85.79.39 1.18l-5.2 4.54 1.54 6.78c.13.59-.51 1.05-1.02.73L12 18.15l-5.45 3.51c-.51.32-1.15-.14-1.02-.73l1.54-6.78-5.2-4.54c-.46-.39-.21-1.13.39-1.18l6.89-.61L12 1.7z"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <div class="stats_total_txt">
                    Com base em <?= $stats['total'] ?> avaliaç<?= $stats['total'] === 1 ? 'ão' : 'ões' ?>
                </div>
            </div>
        </div>

        <!-- Linhas com cápsulas de estrelas (estilo do mockup fornecido) -->
        <div class="stats_pills_container">
            <?php for ($num = 5; $num >= 1; $num--): ?>
                <?php
                    $qtd = (int)($stats['estrelas_' . $num] ?? 0);
                    $porcentagem = $stats['total'] > 0 ? round(($qtd / $stats['total']) * 100) : 0;
                ?>
                <div class="stats_pill_linha">
                    <div class="estrela_pill_mini" title="<?= $num ?> estrelas">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <svg class="estrela_svg_mini <?= $s <= $num ? 'amarela' : 'cinza' ?>" viewBox="0 0 24 24">
                                <path d="M12 1.7l2.85 6.32 6.89.61c.6.05.85.79.39 1.18l-5.2 4.54 1.54 6.78c.13.59-.51 1.05-1.02.73L12 18.15l-5.45 3.51c-.51.32-1.15-.14-1.02-.73l1.54-6.78-5.2-4.54c-.46-.39-.21-1.13.39-1.18l6.89-.61L12 1.7z"/>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <div class="barra_estrela_track">
                        <div class="barra_estrela_fill" style="width: <?= $porcentagem ?>%;"></div>
                    </div>
                    <span class="barra_estrela_qtd"><?= $qtd ?> (<?= $porcentagem ?>%)</span>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Gráfico interativo Chart.js -->
        <div class="grafico_container_avalie">
            <div class="grafico_header_tabs">
                <span class="grafico_titulo">Distribuição de notas</span>
                <div class="grafico_abas_btn">
                    <button type="button" class="btn_tab_grafico ativo" id="btn_modo_barras">Barras</button>
                    <button type="button" class="btn_tab_grafico" id="btn_modo_pizza">Pizza</button>
                </div>
            </div>

            <div class="canvas_chart_wrap">
                <canvas id="graficoAvaliacoes"></canvas>
            </div>
        </div>
    </div>

    <!-- Formulário com o seletor Pill de estrelas interativo -->
    <div class="card_form_avalie">
        <h3 class="subtitulo_painel">Deixe sua Avaliação</h3>

        <form method="post" action="../saves/save_comentario.php" class="form_painel">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="acao" value="criar">

            <div class="rating_picker_wrap">
                <label class="rotulo_agenda" style="margin: 0 0 4px 0;">Sua nota:</label>

                <!-- Pílula de estrelas (preenchem ao passar o mouse / clicar) -->
                <div class="estrela_pill_wrapper">
                    <div class="estrela_pill" id="estrela_pill_interativo" role="radiogroup" aria-label="Avalie de 1 a 5 estrelas">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="estrela_btn_pill" data-valor="<?= $i ?>" title="<?= $rotulosEstrelas[$i] ?>" aria-label="<?= $i ?> estrela<?= $i > 1 ? 's' : '' ?>">
                                <svg class="estrela_svg" viewBox="0 0 24 24">
                                    <path d="M12 1.7l2.85 6.32 6.89.61c.6.05.85.79.39 1.18l-5.2 4.54 1.54 6.78c.13.59-.51 1.05-1.02.73L12 18.15l-5.45 3.51c-.51.32-1.15-.14-1.02-.73l1.54-6.78-5.2-4.54c-.46-.39-.21-1.13.39-1.18l6.89-.61L12 1.7z"/>
                                </svg>
                            </button>
                        <?php endfor; ?>
                    </div>

                    <!-- Input oculto para submissão da nota -->
                    <input type="hidden" name="comentarios_estrela" id="comentarios_estrela_input" value="<?= $vEstrela ?>">

                    <span class="rating_texto_legenda" id="legenda_estrela">
                        <?= $rotulosEstrelas[$vEstrela] ?? $rotulosEstrelas[5] ?>
                    </span>
                </div>
            </div>

            <div class="campo_painel">
                <label for="coment_texto">Seu comentário:</label>
                <textarea id="coment_texto" name="coment_texto" rows="3" maxlength="255" required
                          placeholder="Conte sua experiência com os agendamentos, serviços ou atendimento..."><?= htmlspecialchars($vTexto) ?></textarea>
                <div class="contador_caracteres">
                    <span id="char_counter">0 / 255</span>
                </div>
            </div>

            <div>
                <button type="submit" class="botao_agenda" style="width: 100%; text-align: center; font-size: 0.95rem; padding: 12px;">Publicar Avaliação</button>
            </div>
        </form>
    </div>
</div>

<!-- Feed de comentários recentes com design aprimorado -->
<div class="secao_comentarios_titulo">
    <h3>Comentários Recentes (<?= count($comentarios) ?>)</h3>
</div>

<?php if (empty($comentarios)): ?>
    <div class="card_agenda" style="text-align: center; padding: 42px 20px; background: #fff; border-radius: 16px;">
        <p style="font-size: 1.15rem; color: #6B2F5C; font-weight: 700; margin-bottom: 6px;">Nenhuma avaliação ainda</p>
        <p class="sub_agenda">Seja a primeira pessoa a avaliar e compartilhar sua experiência no Tesorly!</p>
    </div>
<?php else: ?>
    <div class="feed_comentarios">
        <?php foreach ($comentarios as $c): ?>
            <?php
                $ehMeu = ((int)$c['coment_idusuarios'] === $idUsuarioLogado);
                $nomeAutor = !empty($c['user_nome']) ? $c['user_nome'] : 'Cliente Tesorly';

                // Resolução segura da foto do usuário
                $fotoUrl = '../imagens/fotouser.png';
                if (!empty($c['user_fotos'])) {
                    $nomeArquivoFoto = basename(trim($c['user_fotos']));
                    if (file_exists(RAIZ . '/uploads/usuarios/' . $nomeArquivoFoto)) {
                        $fotoUrl = '../uploads/usuarios/' . $nomeArquivoFoto;
                    }
                }

                $estrelasNota = max(1, min(5, (int)$c['comentarios_estrela']));
            ?>
            <article class="card_comentario">
                <div class="avatar_comentario_wrapper">
                    <img class="avatar_comentario"
                         src="<?= htmlspecialchars($fotoUrl) ?>"
                         alt="Foto de <?= htmlspecialchars($nomeAutor) ?>"
                         onerror="this.onerror=null; this.src='../imagens/fotouser.png';">
                </div>

                <div class="conteudo_comentario">
                    <div class="topo_comentario">
                        <div class="autor_comentario_box">
                            <span class="autor_comentario"><?= htmlspecialchars($nomeAutor) ?></span>
                            <span class="badge_verificado">Verificado</span>
                            <?php if ($ehMeu): ?>
                                <span class="badge_meu_comentario">Você</span>
                            <?php endif; ?>
                        </div>

                        <!-- Cápsula com as estrelas SVG do comentário -->
                        <div class="pill_estrelas_comentario" title="<?= $estrelasNota ?> de 5 estrelas">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <svg class="estrela_svg_mini <?= $s <= $estrelasNota ? 'amarela' : 'cinza' ?>" viewBox="0 0 24 24">
                                    <path d="M12 1.7l2.85 6.32 6.89.61c.6.05.85.79.39 1.18l-5.2 4.54 1.54 6.78c.13.59-.51 1.05-1.02.73L12 18.15l-5.45 3.51c-.51.32-1.15-.14-1.02-.73l1.54-6.78-5.2-4.54c-.46-.39-.21-1.13.39-1.18l6.89-.61L12 1.7z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <p class="texto_comentario"><?= nl2br(htmlspecialchars($c['coment_texto'] ?? '')) ?></p>

                    <?php if ($ehMeu): ?>
                        <div class="rodape_comentario">
                            <form method="post" action="../saves/save_comentario.php" onsubmit="return confirm('Deseja realmente excluir sua avaliação?');" style="margin: 0;">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="idcomentarios" value="<?= (int)$c['idcomentarios'] ?>">
                                <button type="submit" class="btn_deletar_comentario">
                                    Excluir avaliação
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==========================================
    // 1. Contador de Caracteres do Comentário
    // ==========================================
    const textarea = document.getElementById('coment_texto');
    const counter = document.getElementById('char_counter');
    if (textarea && counter) {
        function atualizarContador() {
            counter.textContent = textarea.value.length + ' / 255';
        }
        textarea.addEventListener('input', atualizarContador);
        atualizarContador();
    }

    // ==========================================
    // 2. Seletor de Estrelas Pill com Preenchimento Dinâmico no Hover
    // ==========================================
    const pillContainer = document.getElementById('estrela_pill_interativo');
    const inputEstrela = document.getElementById('comentarios_estrela_input');
    const legenda = document.getElementById('legenda_estrela');
    const rotulos = {
        5: 'Excelente',
        4: 'Muito bom',
        3: 'Bom',
        2: 'Regular',
        1: 'Ruim'
    };

    if (pillContainer && inputEstrela) {
        const botoesEstrela = pillContainer.querySelectorAll('.estrela_btn_pill');
        let notaFixada = parseInt(inputEstrela.value) || 5;

        // Função para preencher de amarelo todas as estrelas até a nota especificada
        function preencherEstrelas(nota, comAnimacao = false) {
            botoesEstrela.forEach(btn => {
                const valor = parseInt(btn.getAttribute('data-valor'));
                if (valor <= nota) {
                    btn.classList.add('amarela');
                    if (comAnimacao && valor === nota) {
                        btn.classList.add('animar_click');
                        setTimeout(() => btn.classList.remove('animar_click'), 280);
                    }
                } else {
                    btn.classList.remove('amarela');
                }
            });
        }

        // Aplica o estado inicial
        preencherEstrelas(notaFixada);
        if (legenda && rotulos[notaFixada]) {
            legenda.textContent = rotulos[notaFixada];
        }

        // Adiciona os ouvintes de preenchimento dinâmico
        botoesEstrela.forEach(btn => {
            const valor = parseInt(btn.getAttribute('data-valor'));

            // Hover: preenche as estrelas até a posição do mouse
            btn.addEventListener('mouseenter', function () {
                preencherEstrelas(valor);
                if (legenda && rotulos[valor]) {
                    legenda.textContent = rotulos[valor];
                }
            });

            // Clique: fixa a nota e aciona animação de pop
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                notaFixada = valor;
                inputEstrela.value = valor;
                preencherEstrelas(notaFixada, true);
                if (legenda && rotulos[notaFixada]) {
                    legenda.textContent = rotulos[notaFixada];
                }
            });
        });

        // Quando o mouse sai do pill, restaura a nota que estava fixada
        pillContainer.addEventListener('mouseleave', function () {
            preencherEstrelas(notaFixada);
            if (legenda && rotulos[notaFixada]) {
                legenda.textContent = rotulos[notaFixada];
            }
        });
    }

    // ==========================================
    // 3. Gráficos de Pessoas por Nota (Chart.js)
    // ==========================================
    const canvasGrafico = document.getElementById('graficoAvaliacoes');
    if (canvasGrafico && typeof Chart !== 'undefined') {
        const dadosAvaliacoes = {
            total: <?= (int)$stats['total'] ?>,
            5: <?= (int)$stats['estrelas_5'] ?>,
            4: <?= (int)$stats['estrelas_4'] ?>,
            3: <?= (int)$stats['estrelas_3'] ?>,
            2: <?= (int)$stats['estrelas_2'] ?>,
            1: <?= (int)$stats['estrelas_1'] ?>,
        };

        const labels = ['5 ★', '4 ★', '3 ★', '2 ★', '1 ★'];
        const valores = [
            dadosAvaliacoes[5],
            dadosAvaliacoes[4],
            dadosAvaliacoes[3],
            dadosAvaliacoes[2],
            dadosAvaliacoes[1]
        ];

        const coresBarras = ['#F5B82A', '#F5B82A', '#F5B82A', '#F5B82A', '#F5B82A'];
        const coresPizza = ['#F5B82A', '#E2A01C', '#CE8913', '#BA730B', '#A65E05'];

        let tipoAtual = 'bar';
        let chartInstance = null;

        function renderizarGrafico(tipo) {
            if (chartInstance) {
                chartInstance.destroy();
            }

            const ctx = canvasGrafico.getContext('2d');

            if (tipo === 'bar') {
                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pessoas',
                            data: valores,
                            backgroundColor: coresBarras,
                            borderRadius: 4,
                            borderSkipped: false,
                            barThickness: 14
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const count = context.raw;
                                        const pct = dadosAvaliacoes.total > 0
                                            ? Math.round((count / dadosAvaliacoes.total) * 100)
                                            : 0;
                                        return ` ${count} pessoa(s) (${pct}%)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    color: '#6B2F5C',
                                    font: { family: 'Segoe UI', weight: '600', size: 11 }
                                },
                                grid: { color: '#ECE7F6' }
                            },
                            y: {
                                ticks: {
                                    color: '#2E2340',
                                    font: { family: 'Segoe UI', weight: '700', size: 12 }
                                },
                                grid: { display: false }
                            }
                        }
                    }
                });
            } else {
                chartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: valores,
                            backgroundColor: coresPizza,
                            borderWidth: 2,
                            borderColor: '#FFFFFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 12,
                                    color: '#2E2340',
                                    font: { family: 'Segoe UI', weight: '600', size: 11 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const count = context.raw;
                                        const pct = dadosAvaliacoes.total > 0
                                            ? Math.round((count / dadosAvaliacoes.total) * 100)
                                            : 0;
                                        return ` ${context.label}: ${count} pessoa(s) (${pct}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        }

        renderizarGrafico('bar');

        const btnBarras = document.getElementById('btn_modo_barras');
        const btnPizza = document.getElementById('btn_modo_pizza');

        if (btnBarras && btnPizza) {
            btnBarras.addEventListener('click', function () {
                if (tipoAtual !== 'bar') {
                    tipoAtual = 'bar';
                    btnBarras.classList.add('ativo');
                    btnPizza.classList.remove('ativo');
                    renderizarGrafico('bar');
                }
            });

            btnPizza.addEventListener('click', function () {
                if (tipoAtual !== 'doughnut') {
                    tipoAtual = 'doughnut';
                    btnPizza.classList.add('ativo');
                    btnBarras.classList.remove('ativo');
                    renderizarGrafico('doughnut');
                }
            });
        }
    }
});
</script>

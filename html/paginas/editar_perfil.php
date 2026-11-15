<?php
require_once '../funcoes.php';

$id = (int)$_SESSION['id'];   // vem da sessão: cada um edita só o próprio perfil
$mensagem = '';
$tipo = 'erro';

$usuario = pesquisaruserid($conexao, $id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $telefone   = trim($_POST['telefone'] ?? '');
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha  = $_POST['nova_senha'] ?? '';
    $confirmar  = $_POST['confirmar_senha'] ?? '';

    $foto = null;        // nome da foto nova (null = mantém a atual)
    $senhaHash = null;   // hash da senha nova (null = mantém a atual)
    $enviouFoto = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;

    // ---------- validações ----------
    if ($nome === '' || $telefone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Preencha nome, e-mail e telefone corretamente.';
    } elseif (!password_verify($senhaAtual, (string)$usuario['user_senha'])) {
        $mensagem = 'Senha atual incorreta.';
    } elseif ($novaSenha !== '' && (strlen($novaSenha) < 8 || !preg_match('/[0-9]/', $novaSenha) || !preg_match('/[A-Z]/', $novaSenha))) {
        $mensagem = 'A nova senha precisa de 8+ caracteres, 1 número e 1 letra maiúscula.';
    } elseif ($novaSenha !== $confirmar) {
        $mensagem = 'A confirmação da nova senha não confere.';
    } elseif ($enviouFoto && ($erroFoto = usuarioValidarFoto($_FILES['foto'])) !== null) {
        $mensagem = $erroFoto;
    }

    // ---------- salvar ----------
    if ($mensagem === '') {
        if ($enviouFoto) {
            $info = getimagesize($_FILES['foto']['tmp_name']);
            $extensoes = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
            $foto = 'user_' . bin2hex(random_bytes(8)) . '.' . $extensoes[$info[2]];

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], RAIZ . '/uploads/usuarios/' . $foto)) {
                $foto = null;
                $mensagem = 'Não foi possível salvar a foto no servidor.';
            }
        }

        if ($mensagem === '') {
            if ($novaSenha !== '') {
                $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            }

            try {
                if (editaruser($conexao, $nome, $email, $telefone, $foto, $senhaHash, $id)) {
                    $tipo = 'ok';
                    $mensagem = 'Perfil atualizado com sucesso!';
                    $_SESSION['usuario'] = $nome;

                    if ($foto !== null && !empty($usuario['user_fotos'])) {
                        @unlink(RAIZ . '/uploads/usuarios/' . basename($usuario['user_fotos'])); // apaga a foto antiga
                    }
                    $usuario = pesquisaruserid($conexao, $id); // recarrega com os dados novos
                } else {
                    $mensagem = 'Erro ao atualizar o perfil.';
                }
            } catch (mysqli_sql_exception $e) {
                $mensagem = ($e->getCode() == 1062) ? 'Este e-mail já está cadastrado.' : 'Erro ao salvar no banco.';
            }

            // não deu certo: apaga a foto nova pra não ficar arquivo solto
            if ($tipo !== 'ok' && $foto !== null) {
                @unlink(RAIZ . '/uploads/usuarios/' . $foto);
            }
        }
    }
}

// valores do formulário: o que a pessoa digitou (se deu erro) ou o que está salvo
$vNome     = $_POST['nome']     ?? $usuario['user_nome'];
$vEmail    = $_POST['email']    ?? $usuario['user_email'];
$vTelefone = $_POST['telefone'] ?? $usuario['user_telefone'];
?>
<h2 class="titulo_pagina_agenda">Editar perfil</h2>

<?php if ($mensagem): ?>
    <p class="flash_painel flash_<?= $tipo ?>" role="status"><?= htmlspecialchars($mensagem) ?></p>
<?php endif; ?>

<form class="card_agenda form_painel" method="post" enctype="multipart/form-data">
    <div class="campo_painel">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars((string)$vNome) ?>">
    </div>

    <div class="campo_painel">
        <label for="email">E-mail</label>
        <input type="text" id="email" name="email" required value="<?= htmlspecialchars((string)$vEmail) ?>">
    </div>

    <div class="campo_painel">
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" maxlength="14" required value="<?= htmlspecialchars((string)$vTelefone) ?>">
    </div>

    <div class="campo_painel">
        <label for="foto">Foto de perfil <span class="opcional_painel">(opcional — JPG, PNG ou WEBP, até 5 MB)</span></label>
        <?php if (!empty($usuario['user_fotos'])): ?>
            <div class="foto_atual_painel">
                <img src="../uploads/usuarios/<?= htmlspecialchars($usuario['user_fotos']) ?>" alt="Foto atual">
                <small>Foto atual. Envie outra só se quiser trocar.</small>
            </div>
        <?php endif; ?>
        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
    </div>

    <div class="campo_painel">
        <label for="nova_senha">Nova senha <span class="opcional_painel">(deixe em branco para manter a atual)</span></label>
        <input type="password" id="nova_senha" name="nova_senha" autocomplete="new-password">
        <small>8+ caracteres, 1 número e 1 letra maiúscula.</small>
    </div>

    <div class="campo_painel">
        <label for="confirmar_senha">Confirmar nova senha</label>
        <input type="password" id="confirmar_senha" name="confirmar_senha" autocomplete="new-password">
    </div>

    <div class="campo_painel">
        <label for="senha_atual">Senha atual <span class="opcional_painel">(obrigatória para salvar)</span></label>
        <input type="password" id="senha_atual" name="senha_atual" required autocomplete="current-password">
    </div>

    <div class="botoes_painel">
        <button type="submit" class="botao_agenda">Salvar</button>
        <a class="botao_agenda botao_sec_agenda" href="?pagina=perfil">Cancelar</a>
    </div>
</form>
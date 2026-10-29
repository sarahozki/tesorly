<?php
// Recebe o formulário de serviços (criar, editar, excluir). Só empresário.
session_start();
require_once __DIR__ . '/../funcoes.php';

$app = '../app/index.php';
$voltar = $app . '?pagina=meus_servicos';

function voltarServicos($destino, $tipo, $texto, $dados = []) {
    painelFlashDefinir($tipo, $texto, $dados);
    header('Location: ' . $destino);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['id'])) {
    header('Location: ' . $app);
    exit;
}

// Foto acima do post_max_size: o PHP descarta TUDO (o $_POST chega vazio) e o formulário pareceria "não funcionar"
if (empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    voltarServicos($voltar, 'erro', 'O envio passou do limite do servidor (provavelmente a foto é grande demais). Use uma foto menor.');
}

$idempresario = agendaIdEmpresarioDoUsuario($conexao, (int)$_SESSION['id']);
if ($idempresario === null) {
    header('Location: ' . $app); // cliente comum não tem acesso
    exit;
}

if (!csrfValido($_POST['csrf'] ?? null)) {
    voltarServicos($voltar, 'erro', 'Sua sessão expirou. Tente de novo.', $_POST);
}

$acao = is_string($_POST['acao'] ?? null) ? $_POST['acao'] : '';
$idservico = (int)($_POST['id'] ?? 0);

try {

    // ---------- excluir ----------
    if ($acao === 'excluir') {
        $atual = servicoBuscarDoEmpresario($conexao, $idservico, $idempresario);
        if (!$atual) {
            voltarServicos($voltar, 'erro', 'Serviço não encontrado.');
        }
        if (servicoTemAgendamentos($conexao, $idservico)) {
            voltarServicos($voltar, 'erro',
                'Este serviço já tem agendamentos e não pode ser excluído (os agendamentos seriam apagados junto). Edite o serviço se precisar mudar alguma coisa.');
        }
        if (servicoExcluirDoEmpresario($conexao, $idservico, $idempresario)) {
            servicoRemoverFoto($atual['serv_fotos']);
            voltarServicos($voltar, 'ok', 'Serviço excluído.');
        }
        voltarServicos($voltar, 'erro', 'Não foi possível excluir agora.');
    }

    // ---------- criar / editar ----------
    if ($acao !== 'criar' && $acao !== 'editar') {
        voltarServicos($voltar, 'erro', 'Ação inválida.');
    }

    $campos = servicoValidarCampos($_POST);
    if ($campos['erro'] !== null) {
        voltarServicos($voltar, 'erro', $campos['erro'], $_POST); // mantém o que a pessoa digitou
    }

    $atual = null;
    if ($acao === 'editar') {
        $atual = servicoBuscarDoEmpresario($conexao, $idservico, $idempresario);
        if (!$atual) {
            voltarServicos($voltar, 'erro', 'Serviço não encontrado.');
        }
    }

    // foto (opcional): primeiro o motivo, se houver problema; depois salva
    $erroFoto = servicoValidarFoto($_FILES['foto'] ?? null);
    if ($erroFoto !== null) {
        voltarServicos($voltar, 'erro', $erroFoto, $_POST);
    }
    // null = não enviou foto | false = não conseguiu gravar o arquivo | string = nome salvo
    $foto = servicoUploadFoto($_FILES['foto'] ?? null);
    if ($foto === false) {
        voltarServicos($voltar, 'erro', 'Não foi possível salvar a foto no servidor. Verifique a permissão de escrita da pasta uploads/servicos.', $_POST);
    }

    if ($acao === 'criar') {
        $ok = servicoCriar($conexao, $idempresario, $campos['nome'], $campos['valor'], $campos['descricao'], $campos['tempo'], $foto);
        if (!$ok) {
            servicoRemoverFoto($foto);
            voltarServicos($voltar, 'erro', 'Não foi possível cadastrar agora.', $_POST);
        }
        voltarServicos($voltar, 'ok', 'Serviço cadastrado! Ele já aparece no seu perfil para os clientes.');
    }

    // editar
    $ok = servicoAtualizar($conexao, $idservico, $idempresario, $campos['nome'], $campos['valor'], $campos['descricao'], $campos['tempo'], $foto);
    if (!$ok) {
        servicoRemoverFoto($foto);
        voltarServicos($voltar, 'erro', 'Não foi possível salvar agora.', $_POST);
    }
    if ($foto !== null) {
        servicoRemoverFoto($atual['serv_fotos']); // trocou a foto: apaga a antiga
    }
    voltarServicos($voltar, 'ok', 'Serviço atualizado!');

} catch (Throwable $erro) {
    // (exit dentro de voltarServicos não passa por aqui) erro de verdade: banco, arquivo...
    if (isset($foto) && is_string($foto)) {
        servicoRemoverFoto($foto); // não deixa foto órfã
    }
    voltarServicos($voltar, 'erro', painelErroParaTela($erro, 'save_servico'), $_POST);
}

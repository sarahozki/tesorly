<?php

    require_once("conexao.php");
//--------------------USERS-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------USERS-----------------------//
function verificarLogin(){
    // return isset($_SESSION['usuario']);
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../login.php");
    exit;
    }
}

function deletaruser($conexao, $idusuarios) {
        $sql = "DELETE FROM usuarios WHERE idusuarios = ?";
        $comando = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($comando, 'i', $idusuarios);
        $funcionou = mysqli_stmt_execute($comando);
        mysqli_stmt_close($comando);
        return $funcionou; //true ou false
    };

function listaruser($conexao) {
    $sql = "SELECT * FROM usuarios";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $listar_usuarios = [];

    while ($usuarios = mysqli_fetch_assoc($resultado)) {
        $listar_usuarios[] = $usuarios;
    }

    mysqli_stmt_close($comando);
    return $listar_usuarios;
}

//string s numero i
function salvaruser($conexao, $nome, $email, $senha, $telefone, $fotos) {

    // 1. Definir pasta de destino (ajuste o caminho conforme sua estrutura)
    $pasta_destino = '../uploads/usuarios/';

    // Cria a pasta se não existir
    if (!is_dir($pasta_destino)) {
        mkdir($pasta_destino, 0755, true);
    }

    // 2. Validar se o upload realmente ocorreu sem erro
    if ($fotos['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // 3. Gerar nome único pra evitar sobrescrever arquivos com mesmo nome
    $extensao = pathinfo($fotos['name'], PATHINFO_EXTENSION);
    $nome_arquivo = uniqid('user_', true) . '.' . $extensao;
    $caminho_completo = $pasta_destino . $nome_arquivo;

    // 4. Mover o arquivo do temporário pra pasta definitiva
    if (!move_uploaded_file($fotos['tmp_name'], $caminho_completo)) {
        return false;
    }

    // 5. Agora sim, salvar o NOME (string) no banco
    $sql = "INSERT INTO usuarios (user_nome, user_email, user_senha, user_telefone, user_fotos) VALUES (?, ?, ?, ?, ?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'sssss', $nome, $email, $senha, $telefone, $nome_arquivo);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);

    // Se falhar ao salvar no banco, remove o arquivo que foi movido (opcional, mas recomendado)
    if (!$funcionou) {
        unlink($caminho_completo);
    }

    return $funcionou;

    $tipos_permitidos = ['jpg', 'jpeg', 'png', 'webp'];
if (!in_array(strtolower($extensao), $tipos_permitidos)) {
    return false;}
}

function editaruser($conexao, $nome, $email, $senha, $telefone, $fotos, $idusuarios){
    $sql = "UPDATE usuarios SET nome=?, email=?, senha=?, telefone=?, fotos=? WHERE idusuarios=?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'sssi', $nome, $email, $senha, $telefone, $fotos, $idusuarios);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};

function pesquisaruserid($conexao, $idusuarios){
    $sql = "SELECT * FROM usuarios WHERE idusuarios =?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idusuarios);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $usuarios = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    return $usuarios;
};

//--------------------PRODUTOS-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------PRODUTOS-----------------------//


function deletarprodutos($conexao, $idprodutos) {
    $sql = "DELETE FROM produtos WHERE idprodutos = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idprodutos);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou; //true ou false
};

function listarprodutos($conexao) {
    $sql = "SELECT * FROM produtos";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista_produtos = [];
    while ($produtos = mysqli_fetch_assoc($resultado)) {
        $lista_produtos[] = $produtos;
    }
    mysqli_stmt_close($comando);
    return $lista_produtos;
};

function salvarprodutos($conexao, $nome, $marca, $fotos, $empresario_idempresario) {
    $sql = "INSERT INTO produtos (prod_nome, prod_marca, prod_fotos, empresario_idempresario) VALUES (?, ?, ?, ?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando,'sssi', $nome, $marca, $fotos, $empresario_idempresario);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};

function editarprodutos($conexao, $nome, $marca, $fotos, $empresario_idempresario, $idprodutos) {
    $sql = "UPDATE produtos SET nome=?, marca=?, fotos=?, empresario_idempresario=? WHERE idprodutos=?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando,'sssi', $nome, $marca, $fotos, $empresario_idempresario, $idprodutos);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};

function pesquisarprodutosId($conexao,$idprodutos) {
    $sql = "SELECT * FROM produtos WHERE idprodutos =?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idprodutos);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $produtos = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    return $produtos;
};

//--------------------SERVIÇOS-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------SERVIÇOS-----------------------//


function salvarservico( $conexao,$servico,$valor,$descricao,$tempo,$fotos){
    $sql = "INSERT INTO mydb.servicos
    (serv_nome, serv_valor, serv_descricao, serv_tempo, serv_fotos)
    VALUES (?, ?, ?, ?,?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param( $comando,'sdsss',$servico, $valor, $descricao, $tempo, $fotos);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
}

function deletarservico($conexao, $idservicos) {
    $sql = "DELETE FROM servicos WHERE idservicos = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idservicos);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou; //true ou false
};

function listarservico($conexao) {
    $sql = "SELECT * FROM servicos";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $listar_servicos = [];
    while ($servicos = mysqli_fetch_assoc($resultado)) {
        $listar_servicos[] = $servicos;
    }
    mysqli_stmt_close($comando);
    return $listar_servicos;
};

function editarservicos($conexao, $nome, $valor, $descricao, $tempo, $fotos, $agenda_idagenda, $idservicos) {
    $sql = "UPDATE servicos SET nome=?, valor=?, descricao=?, tempo=?, fotos=?, agenda_idagenda=? WHERE idservicos=?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando,'sdsss', $nome, $valor, $descricao, $tempo, $fotos, $agenda_idagenda, $idservicos);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};


//--------------------EMPRESÁRIO-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------EMPRESÁRIO-----------------------//

function deletarempresario($conexao, $idempresario) {
    $sql = "DELETE FROM empresario WHERE idempresario = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idempresario);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou; //true ou false
};

function listarempresario($conexao) {
    $sql = "SELECT * FROM empresario";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $listar_empresario = [];
    while ($empresario = mysqli_fetch_assoc($resultado)) {
        $listar_empresario[] = $empresario;
    }
    mysqli_stmt_close($comando);
    return $listar_empresario;
};

function editarempresario($conexao, $empre_tipo, $empre_dias_trab, $cidade, $idempresario) {
    $sql = "UPDATE empresario SET tipo=?, diastrabalho=?, cidade=? WHERE idempresario=?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando,'sss', $empre_tipo, $empre_dias_trab, $cidade, $idempresario);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};


function salvarempresario($conexao, $usuarios_idusuarios, $empre_tipo, $dias_trab, $cidade) {
    $sql = "INSERT INTO empresario (usuarios_idusuarios, empre_tipo, empre_dias_trab, empre_cidade) VALUES (?, ?, ?, ?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'isss', $usuarios_idusuarios, $empre_tipo, $dias_trab, $cidade);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
}


//--------------------AGENDA-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------AGENDA-----------------------//


function deletaragenda($conexao, $idagenda) {
    $sql = "DELETE FROM agenda WHERE idagenda = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idagenda);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou; //true ou false
};

function listaragenda($conexao) {
    $sql = "SELECT * FROM agenda ";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $listar_agenda = [];
    while ($agenda = mysqli_fetch_assoc($resultado)) {
        $listar_agenda[] = $agenda;
    }
    mysqli_stmt_close($comando);
    return $listar_agenda;
};

function editaragenda($conexao, $horario, $dia, $agenda_idusuario, $idagenda) {
    $sql = "UPDATE agenda SET horario=?, dia=?, agenda_idusuario=? WHERE idempresario=?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando,'isii', $horario, $dia, $agenda_idusuario, $idagenda);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};


//--------------------AGENDAMENTO (horários de 30 em 30 min)-----------------------//
// Regras:
// - o expediente do profissional vem da tabela `horarios` (um ou mais turnos por dia da semana)
// - a agenda é dividida em horários de 30 em 30 minutos
// - um serviço ocupa quantos horários forem necessários (45 min ocupa 2 horários = 60 min)
// - agendamentos cancelados liberam o horário
//--------------------AGENDAMENTO-----------------------//

date_default_timezone_set('America/Sao_Paulo');

const AGENDA_INTERVALO_MIN = 30;  // tamanho de cada horário
const AGENDA_DIAS_MAX      = 60;  // até quantos dias à frente dá pra marcar

function agendaResultado($sucesso, $mensagem, $idagenda = 0) {
    return ['sucesso' => $sucesso, 'mensagem' => $mensagem, 'idagenda' => $idagenda];
}

// 'HH:MM' ou 'HH:MM:SS' -> minutos desde 00:00
function agendaMinutos($hora) {
    $partes = explode(':', $hora);
    return ((int)$partes[0]) * 60 + (int)($partes[1] ?? 0);
}

// minutos desde 00:00 -> 'HH:MM'
function agendaHoraTexto($minutos) {
    return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
}

// quantos horários de 30 min o serviço ocupa (mínimo 1)
function agendaBlocos($serv_tempo) {
    return max(1, (int)ceil(agendaMinutos($serv_tempo) / AGENDA_INTERVALO_MIN));
}

function agendaDuracaoTexto($serv_tempo) {
    $min = agendaMinutos($serv_tempo);
    if ($min < 60) {
        return $min . ' min';
    }
    $h = intdiv($min, 60);
    $m = $min % 60;
    return $m ? $h . 'h ' . $m . 'min' : $h . 'h';
}

function agendaStatusTexto($status) {
    $textos = ['pendente' => 'Pendente', 'confirmado' => 'Confirmado', 'cancelado' => 'Cancelado', 'concluido' => 'Concluído'];
    return $textos[$status] ?? $status;
}

// 'Segunda-feira' / 'terça' / 'sabado' -> 'seg' / 'ter' / 'sab' (aceita com ou sem acento)
function agendaNormalizarDia($texto) {
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    $texto = strtr($texto, ['á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e',
                            'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ç' => 'c']);
    return substr($texto, 0, 3);
}

function agendaDataValida($data) {
    $d = DateTime::createFromFormat('!Y-m-d', $data);
    return $d !== false && $d->format('Y-m-d') === $data;
}

// 'Y-m-d' -> 'dom','seg','ter','qua','qui','sex','sab'
function agendaCodigoDia($data) {
    $codigos = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'];
    $d = DateTime::createFromFormat('!Y-m-d', $data);
    return $codigos[(int)$d->format('w')];
}

// ---------- profissionais e serviços ----------

function agendaListarProfissionais($conexao) {
    $sql = "SELECT e.idempresario, e.empre_tipo, e.empre_cidade, u.user_nome, u.user_fotos
            FROM empresario e
            JOIN usuarios u ON u.idusuarios = e.usuarios_idusuarios
            ORDER BY u.user_nome";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $lista[] = $linha;
    }
    mysqli_stmt_close($comando);
    return $lista;
}

function agendaBuscarProfissional($conexao, $idempresario) {
    $sql = "SELECT e.idempresario, e.empre_tipo, e.empre_cidade, u.user_nome, u.user_fotos
            FROM empresario e
            JOIN usuarios u ON u.idusuarios = e.usuarios_idusuarios
            WHERE e.idempresario = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idempresario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $profissional = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    return $profissional; // array ou null
}

// id do empresário ligado a um usuário (ou null se o usuário é só cliente)
function agendaIdEmpresarioDoUsuario($conexao, $idusuario) {
    $sql = "SELECT idempresario FROM empresario WHERE usuarios_idusuarios = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idusuario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $linha = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    return $linha ? (int)$linha['idempresario'] : null;
}

function agendaServicosDoProfissional($conexao, $idempresario) {
    $sql = "SELECT idservicos, serv_nome, serv_valor, serv_descricao, serv_tempo, serv_fotos
            FROM servicos
            WHERE empresario_idempresario = ?
            ORDER BY serv_nome";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idempresario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $lista[] = $linha;
    }
    mysqli_stmt_close($comando);
    return $lista;
}

// só devolve o serviço se ele for DESSE profissional
function agendaBuscarServico($conexao, $idservico, $idempresario) {
    $sql = "SELECT idservicos, serv_nome, serv_valor, serv_tempo
            FROM servicos
            WHERE idservicos = ? AND empresario_idempresario = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'ii', $idservico, $idempresario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $servico = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    return $servico; // array ou null
}

// ---------- cálculo dos horários ----------

// Expediente do profissional: ['seg' => [[540, 720], [810, 1080]], 'sab' => [[540, 780]], ...] (minutos)
function agendaExpediente($conexao, $idempresario) {
    $sql = "SELECT horarios_dia_semana, horarios_hora_inicio, horarios_hora_fim
            FROM horarios
            WHERE empresario_idempresario = ?
            ORDER BY horarios_hora_inicio";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idempresario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $expediente = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $dia = agendaNormalizarDia($linha['horarios_dia_semana']);
        $expediente[$dia][] = [agendaMinutos($linha['horarios_hora_inicio']), agendaMinutos($linha['horarios_hora_fim'])];
    }
    mysqli_stmt_close($comando);
    return $expediente;
}

// Intervalos [inicio, fim) em minutos já ocupados no dia (agendamentos não cancelados)
function agendaOcupados($conexao, $idempresario, $data) {
    $sql = "SELECT a.agend_horario, s.serv_tempo
            FROM agenda a
            JOIN servicos s ON s.idservicos = a.agend_idservicos
            WHERE a.agend_idempresario = ?
              AND a.agend_horario >= ? AND a.agend_horario <= ?
              AND a.agend_status <> 'cancelado'";
    $comando = mysqli_prepare($conexao, $sql);
    $inicioDia = $data . ' 00:00:00';
    $fimDia = $data . ' 23:59:59';
    mysqli_stmt_bind_param($comando, 'iss', $idempresario, $inicioDia, $fimDia);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $ocupados = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $inicio = agendaMinutos(substr($linha['agend_horario'], 11, 5));
        $ocupados[] = [$inicio, $inicio + agendaBlocos($linha['serv_tempo']) * AGENDA_INTERVALO_MIN];
    }
    mysqli_stmt_close($comando);
    return $ocupados;
}

// Todos os horários do dia para aquele serviço, cada um marcado como livre ou não:
// [['hora' => '09:00', 'livre' => true, 'motivo' => null], ['hora' => '09:30', 'livre' => false, 'motivo' => 'ocupado'], ...]
// motivo: 'ocupado' (ele ou os horários seguintes que o serviço precisa já estão tomados)
//         'encerra' (o serviço não cabe antes do fim do turno)
// Horários que já passaram (hoje) não aparecem. Retorna [] se o profissional não atende no dia.
function agendaHorariosDoDia($conexao, $idempresario, $data, $idservico) {
    if (!agendaDataValida($data)) {
        return [];
    }
    $hoje = date('Y-m-d');
    $limite = date('Y-m-d', strtotime('+' . AGENDA_DIAS_MAX . ' days'));
    if ($data < $hoje || $data > $limite) {
        return [];
    }

    $servico = agendaBuscarServico($conexao, $idservico, $idempresario);
    if (!$servico) {
        return [];
    }

    $expediente = agendaExpediente($conexao, $idempresario);
    $turnos = $expediente[agendaCodigoDia($data)] ?? [];
    if (!$turnos) {
        return [];
    }

    $duracao = agendaBlocos($servico['serv_tempo']) * AGENDA_INTERVALO_MIN;
    $ocupados = agendaOcupados($conexao, $idempresario, $data);
    $agora = ($data === $hoje) ? ((int)date('G') * 60 + (int)date('i')) : -1;

    $horarios = [];
    foreach ($turnos as [$inicioTurno, $fimTurno]) {
        for ($inicio = $inicioTurno; $inicio < $fimTurno; $inicio += AGENDA_INTERVALO_MIN) {
            if ($inicio <= $agora) {
                continue; // já passou
            }
            $fim = $inicio + $duracao;
            $motivo = null;

            if ($fim > $fimTurno) {
                $motivo = 'encerra';
            } else {
                foreach ($ocupados as [$ocupadoInicio, $ocupadoFim]) {
                    if ($inicio < $ocupadoFim && $fim > $ocupadoInicio) {
                        $motivo = 'ocupado';
                        break;
                    }
                }
            }

            $horarios[] = ['hora' => agendaHoraTexto($inicio), 'livre' => ($motivo === null), 'motivo' => $motivo];
        }
    }
    return $horarios;
}

// ---------- criar / alterar agendamentos ----------

// Cria o agendamento (status 'pendente'). Retorna ['sucesso' => bool, 'mensagem' => string, 'idagenda' => int]
function agendaCriar($conexao, $idusuario, $idempresario, $idservico, $data, $hora) {
    if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
        return agendaResultado(false, 'Horário inválido.');
    }

    try {
        mysqli_begin_transaction($conexao);

        // Trava a linha do profissional até o fim da transação: se dois clientes clicarem no mesmo
        // horário ao mesmo tempo, o segundo espera o primeiro terminar e então enxerga o horário ocupado.
        // (Não faça nenhuma leitura comum antes desta linha.)
        $sql = "SELECT idempresario FROM empresario WHERE idempresario = ? FOR UPDATE";
        $comando = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($comando, 'i', $idempresario);
        mysqli_stmt_execute($comando);
        $existe = mysqli_num_rows(mysqli_stmt_get_result($comando)) > 0;
        mysqli_stmt_close($comando);

        if (!$existe) {
            mysqli_rollback($conexao);
            return agendaResultado(false, 'Profissional não encontrado.');
        }

        // revalida com os dados atuais: o horário precisa estar livre pra esse serviço
        $livre = false;
        foreach (agendaHorariosDoDia($conexao, $idempresario, $data, $idservico) as $h) {
            if ($h['hora'] === $hora && $h['livre']) {
                $livre = true;
                break;
            }
        }
        if (!$livre) {
            mysqli_rollback($conexao);
            return agendaResultado(false, 'Esse horário não está mais disponível. Escolha outro.');
        }

        $dataHora = $data . ' ' . $hora . ':00';
        $sql = "INSERT INTO agenda (agend_horario, agend_status, agend_idusuarios, agend_idempresario, agend_idservicos)
                VALUES (?, 'pendente', ?, ?, ?)";
        $comando = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($comando, 'siii', $dataHora, $idusuario, $idempresario, $idservico);
        mysqli_stmt_execute($comando);
        $idagenda = mysqli_stmt_insert_id($comando);
        mysqli_stmt_close($comando);

        mysqli_commit($conexao);
        return agendaResultado(true, 'Agendamento realizado! Você pode acompanhar na página Agenda.', $idagenda);

    } catch (Throwable $erro) {
        mysqli_rollback($conexao);
        return agendaResultado(false, 'Não foi possível agendar agora. Tente novamente.');
    }
}

// $acao: 'cancelar' (cliente ou profissional) ou 'confirmar' (só o profissional)
function agendaAlterarStatus($conexao, $idagenda, $acao, $idusuario) {
    $novoStatus = ['cancelar' => 'cancelado', 'confirmar' => 'confirmado'][(string)$acao] ?? null;
    if ($novoStatus === null) {
        return agendaResultado(false, 'Ação inválida.');
    }

    $sql = "SELECT a.agend_idusuarios AS id_cliente, e.usuarios_idusuarios AS id_profissional, a.agend_status
            FROM agenda a
            JOIN empresario e ON e.idempresario = a.agend_idempresario
            WHERE a.idagenda = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idagenda);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $agendamento = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);

    if (!$agendamento) {
        return agendaResultado(false, 'Agendamento não encontrado.');
    }

    $ehCliente = ((int)$agendamento['id_cliente'] === (int)$idusuario);
    $ehProfissional = ((int)$agendamento['id_profissional'] === (int)$idusuario);
    $permitido = ($acao === 'cancelar') ? ($ehCliente || $ehProfissional) : $ehProfissional;

    if (!$permitido) {
        return agendaResultado(false, 'Você não tem permissão para fazer isso.');
    }
    if (in_array($agendamento['agend_status'], ['cancelado', 'concluido'], true)) {
        return agendaResultado(false, 'Esse agendamento não pode mais ser alterado.');
    }

    $sql = "UPDATE agenda SET agend_status = ? WHERE idagenda = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'si', $novoStatus, $idagenda);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);

    return agendaResultado($funcionou, $funcionou ? 'Agendamento atualizado.' : 'Não foi possível atualizar.', $idagenda);
}

// ---------- listas ----------

// agendamentos que o usuário fez como cliente
function agendaListarPorUsuario($conexao, $idusuario) {
    $sql = "SELECT a.idagenda, a.agend_horario, a.agend_status,
                   u.user_nome AS profissional, e.empre_tipo, e.empre_cidade,
                   s.serv_nome, s.serv_valor, s.serv_tempo
            FROM agenda a
            JOIN empresario e ON e.idempresario = a.agend_idempresario
            JOIN usuarios u ON u.idusuarios = e.usuarios_idusuarios
            JOIN servicos s ON s.idservicos = a.agend_idservicos
            WHERE a.agend_idusuarios = ?
            ORDER BY a.agend_horario DESC
            LIMIT 50";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idusuario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $lista[] = $linha;
    }
    mysqli_stmt_close($comando);
    return $lista;
}

// agendamentos que o usuário recebeu como profissional (de hoje em diante, sem os cancelados)
function agendaListarRecebidos($conexao, $idusuario) {
    $sql = "SELECT a.idagenda, a.agend_horario, a.agend_status,
                   c.user_nome AS cliente, c.user_telefone,
                   s.serv_nome, s.serv_valor, s.serv_tempo
            FROM agenda a
            JOIN empresario e ON e.idempresario = a.agend_idempresario
            JOIN usuarios c ON c.idusuarios = a.agend_idusuarios
            JOIN servicos s ON s.idservicos = a.agend_idservicos
            WHERE e.usuarios_idusuarios = ?
              AND a.agend_horario >= ?
              AND a.agend_status <> 'cancelado'
            ORDER BY a.agend_horario ASC
            LIMIT 100";
    $comando = mysqli_prepare($conexao, $sql);
    $desdeHoje = date('Y-m-d') . ' 00:00:00';
    mysqli_stmt_bind_param($comando, 'is', $idusuario, $desdeHoje);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $lista[] = $linha;
    }
    mysqli_stmt_close($comando);
    return $lista;
}


//--------------------PAINEL DO EMPRESÁRIO (horários e serviços)-----------------------//
// Usado pelas páginas "Meus horários" e "Meus serviços" e pelos scripts em saves/.
// Só o empresário vê essas páginas: quem é empresário é decidido por agendaIdEmpresarioDoUsuario().
//--------------------PAINEL DO EMPRESÁRIO-----------------------//

const SERVICO_DURACAO_MIN = 30;   // menor duração e passo do campo (a agenda é de 30 em 30)
const SERVICO_DURACAO_MAX = 480;  // 8 horas

// ---------- segurança e mensagens ----------

function csrfToken() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfValido($token) {
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

// Mensagem que aparece UMA vez na próxima página (depois do redirect). $dados guarda o que a pessoa digitou.
function painelFlashDefinir($tipo, $texto, $dados = []) {
    $_SESSION['flash_painel'] = ['tipo' => $tipo, 'texto' => $texto, 'dados' => $dados];
}

function painelFlashLer() {
    $flash = $_SESSION['flash_painel'] ?? null;
    unset($_SESSION['flash_painel']);
    return $flash;
}

// ---------- horários de atendimento ----------

// Dias na ordem em que aparecem no formulário: código curto => [nome, abreviação, valor gravado no banco]
function painelDiasSemana() {
    return [
        'seg' => ['nome' => 'Segunda-feira', 'curto' => 'Seg', 'valor' => 'segunda'],
        'ter' => ['nome' => 'Terça-feira',   'curto' => 'Ter', 'valor' => 'terca'],
        'qua' => ['nome' => 'Quarta-feira',  'curto' => 'Qua', 'valor' => 'quarta'],
        'qui' => ['nome' => 'Quinta-feira',  'curto' => 'Qui', 'valor' => 'quinta'],
        'sex' => ['nome' => 'Sexta-feira',   'curto' => 'Sex', 'valor' => 'sexta'],
        'sab' => ['nome' => 'Sábado',        'curto' => 'Sáb', 'valor' => 'sabado'],
        'dom' => ['nome' => 'Domingo',       'curto' => 'Dom', 'valor' => 'domingo'],
    ];
}

// 'HH:MM' (só :00 ou :30) -> minutos; null se inválido
function painelHoraParaMinutos($texto) {
    if (!is_string($texto) || !preg_match('/^([01]\d|2[0-3]):(00|30)$/', $texto, $m)) {
        return null;
    }
    return ((int)$m[1]) * 60 + (int)$m[2];
}

// O que o formulário "Meus horários" deve mostrar: ['seg' => ['t1_ini'=>'09:00','t1_fim'=>'12:00','t2_ini'=>'','t2_fim'=>''], ...]
// Só dias que têm expediente aparecem no resultado.
function painelHorariosParaFormulario($conexao, $idempresario) {
    $expediente = agendaExpediente($conexao, $idempresario); // ['seg' => [[540,720],[810,1080]], ...] em minutos
    $formulario = [];
    foreach ($expediente as $codigo => $turnos) {
        $formulario[$codigo] = [
            't1_ini' => agendaHoraTexto($turnos[0][0]),
            't1_fim' => agendaHoraTexto($turnos[0][1]),
            't2_ini' => isset($turnos[1]) ? agendaHoraTexto($turnos[1][0]) : '',
            't2_fim' => isset($turnos[1]) ? agendaHoraTexto($turnos[1][1]) : '',
        ];
    }
    return $formulario;
}

// Valida o POST do formulário de horários.
// Retorna ['erro' => null, 'turnos' => ['segunda' => [[540,720],[810,1080]], ...], 'rotulos' => ['Seg','Ter']]
//      ou ['erro' => 'texto', ...]
function painelValidarHorarios($post) {
    $enviado = (isset($post['dia']) && is_array($post['dia'])) ? $post['dia'] : [];
    $turnos = [];
    $rotulos = [];

    foreach (painelDiasSemana() as $codigo => $info) {
        $linha = (isset($enviado[$codigo]) && is_array($enviado[$codigo])) ? $enviado[$codigo] : [];
        if (empty($linha['ativo'])) {
            continue; // dia sem atendimento
        }

        $nome = $info['nome'];
        $t1i = painelHoraParaMinutos($linha['t1_ini'] ?? '');
        $t1f = painelHoraParaMinutos($linha['t1_fim'] ?? '');
        if ($t1i === null || $t1f === null) {
            return ['erro' => $nome . ': informe o início e o fim do atendimento (horas cheias ou meias, ex.: 09:00 e 12:30).'];
        }
        if ($t1i >= $t1f) {
            return ['erro' => $nome . ': o início precisa ser antes do fim.'];
        }
        $dia = [[$t1i, $t1f]];

        $t2iTexto = is_string($linha['t2_ini'] ?? null) ? trim($linha['t2_ini']) : '';
        $t2fTexto = is_string($linha['t2_fim'] ?? null) ? trim($linha['t2_fim']) : '';
        if ($t2iTexto !== '' || $t2fTexto !== '') {
            $t2i = painelHoraParaMinutos($t2iTexto);
            $t2f = painelHoraParaMinutos($t2fTexto);
            if ($t2i === null || $t2f === null) {
                return ['erro' => $nome . ': no segundo turno, preencha o início e o fim (ou deixe os dois em branco).'];
            }
            if ($t2i >= $t2f) {
                return ['erro' => $nome . ': no segundo turno, o início precisa ser antes do fim.'];
            }
            if ($t2i < $t1f) {
                return ['erro' => $nome . ': o segundo turno precisa começar depois do fim do primeiro.'];
            }
            $dia[] = [$t2i, $t2f];
        }

        $turnos[$info['valor']] = $dia;
        $rotulos[] = $info['curto'];
    }

    return ['erro' => null, 'turnos' => $turnos, 'rotulos' => $rotulos];
}

// Troca TODOS os horários do empresário pelos novos (numa transação: ou grava tudo ou nada).
// Agendamentos que já existem não são alterados.
function painelSalvarHorarios($conexao, $idempresario, $turnos, $rotulos) {
    try {
        mysqli_begin_transaction($conexao);

        $comando = mysqli_prepare($conexao, "DELETE FROM horarios WHERE empresario_idempresario = ?");
        mysqli_stmt_bind_param($comando, 'i', $idempresario);
        mysqli_stmt_execute($comando);
        mysqli_stmt_close($comando);

        $comando = mysqli_prepare($conexao,
            "INSERT INTO horarios (horarios_dia_semana, horarios_hora_inicio, horarios_hora_fim, empresario_idempresario)
             VALUES (?, ?, ?, ?)");
        foreach ($turnos as $diaBanco => $lista) {
            foreach ($lista as [$inicio, $fim]) {
                $horaInicio = agendaHoraTexto($inicio) . ':00';
                $horaFim = agendaHoraTexto($fim) . ':00';
                mysqli_stmt_bind_param($comando, 'sssi', $diaBanco, $horaInicio, $horaFim, $idempresario);
                mysqli_stmt_execute($comando);
            }
        }
        mysqli_stmt_close($comando);

        // mantém o texto "Dias de trabalho" do cadastro do empresário coerente com os horários
        $resumo = $rotulos ? implode(', ', $rotulos) : 'Nenhum';
        $comando = mysqli_prepare($conexao, "UPDATE empresario SET empre_dias_trab = ? WHERE idempresario = ?");
        mysqli_stmt_bind_param($comando, 'si', $resumo, $idempresario);
        mysqli_stmt_execute($comando);
        mysqli_stmt_close($comando);

        mysqli_commit($conexao);
        return true;
    } catch (Throwable $erro) {
        mysqli_rollback($conexao);
        return false;
    }
}

// ---------- serviços ----------

// Durações oferecidas no formulário: minutos => texto ("30 min", "1h", "1h 30min"...)
function painelOpcoesDuracao() {
    $opcoes = [];
    for ($min = SERVICO_DURACAO_MIN; $min <= SERVICO_DURACAO_MAX; $min += SERVICO_DURACAO_MIN) {
        $opcoes[$min] = agendaDuracaoTexto(agendaHoraTexto($min));
    }
    return $opcoes;
}

// Foto do serviço. Retorna: null (nenhum arquivo enviado) | false (arquivo inválido) | nome do arquivo salvo.
// Aceita jpg, png e webp de até 2 MB; confere o conteúdo real da imagem, não só a extensão.
function servicoUploadFoto($arquivo) {
    if (!is_array($arquivo) || ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $info = @getimagesize($arquivo['tmp_name']);
    $extensoes = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($extensoes[$info[2]])) {
        return false;
    }

    $pasta = __DIR__ . '/uploads/servicos/';
    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
        return false;
    }

    $nome = 'serv_' . bin2hex(random_bytes(8)) . '.' . $extensoes[$info[2]];
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $nome)) {
        return false;
    }
    return $nome;
}

function servicoRemoverFoto($nome) {
    if (is_string($nome) && preg_match('/^serv_[a-f0-9]{16}\.(jpg|png|webp)$/', $nome)) {
        @unlink(__DIR__ . '/uploads/servicos/' . $nome);
    }
}

// Valida os campos de texto do formulário de serviço.
// Retorna ['erro' => null, 'nome' =>, 'descricao' =>, 'valor' => float, 'tempo' => 'HH:MM:SS']  ou ['erro' => 'texto'].
function servicoValidarCampos($post) {
    $nome = trim($post['nome'] ?? '');
    $descricao = trim($post['descricao'] ?? '');
    $valorTexto = str_replace(',', '.', trim($post['valor'] ?? ''));
    $minutos = (int)($post['duracao'] ?? 0);

    if (mb_strlen($nome) < 2 || mb_strlen($nome) > 100) {
        return ['erro' => 'Informe o nome do serviço (de 2 a 100 caracteres).'];
    }
    if ($descricao === '' || mb_strlen($descricao) > 255) {
        return ['erro' => 'Informe uma descrição de até 255 caracteres.'];
    }
    if (!is_numeric($valorTexto) || (float)$valorTexto <= 0 || (float)$valorTexto > 99999999.99) {
        return ['erro' => 'Informe um valor maior que zero.'];
    }
    if ($minutos < SERVICO_DURACAO_MIN || $minutos > SERVICO_DURACAO_MAX || $minutos % SERVICO_DURACAO_MIN !== 0) {
        return ['erro' => 'Escolha uma duração válida (de 30 em 30 minutos).'];
    }

    return [
        'erro' => null,
        'nome' => $nome,
        'descricao' => $descricao,
        'valor' => round((float)$valorTexto, 2),
        'tempo' => agendaHoraTexto($minutos) . ':00',
    ];
}

function servicoCriar($conexao, $idempresario, $nome, $valor, $descricao, $tempo, $foto) {
    $sql = "INSERT INTO servicos (serv_nome, serv_valor, serv_descricao, serv_tempo, serv_fotos, empresario_idempresario)
            VALUES (?, ?, ?, ?, ?, ?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'sdsssi', $nome, $valor, $descricao, $tempo, $foto, $idempresario);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
}

// só devolve o serviço se ele for DESSE empresário
function servicoBuscarDoEmpresario($conexao, $idservico, $idempresario) {
    $sql = "SELECT idservicos, serv_nome, serv_valor, serv_descricao, serv_tempo, serv_fotos
            FROM servicos WHERE idservicos = ? AND empresario_idempresario = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'ii', $idservico, $idempresario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $servico = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($comando);
    return $servico; // array ou null
}

// $foto: nome do arquivo novo, ou null para manter a foto atual
function servicoAtualizar($conexao, $idservico, $idempresario, $nome, $valor, $descricao, $tempo, $foto) {
    if ($foto === null) {
        $sql = "UPDATE servicos SET serv_nome = ?, serv_valor = ?, serv_descricao = ?, serv_tempo = ?
                WHERE idservicos = ? AND empresario_idempresario = ?";
        $comando = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($comando, 'sdssii', $nome, $valor, $descricao, $tempo, $idservico, $idempresario);
    } else {
        $sql = "UPDATE servicos SET serv_nome = ?, serv_valor = ?, serv_descricao = ?, serv_tempo = ?, serv_fotos = ?
                WHERE idservicos = ? AND empresario_idempresario = ?";
        $comando = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($comando, 'sdsssii', $nome, $valor, $descricao, $tempo, $foto, $idservico, $idempresario);
    }
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
}

// No banco, apagar um serviço apaga em cascata os agendamentos dele. Por isso a tela só deixa excluir
// serviços sem nenhum agendamento.
function servicoTemAgendamentos($conexao, $idservico) {
    $comando = mysqli_prepare($conexao, "SELECT COUNT(*) AS total FROM agenda WHERE agend_idservicos = ?");
    mysqli_stmt_bind_param($comando, 'i', $idservico);
    mysqli_stmt_execute($comando);
    $linha = mysqli_fetch_assoc(mysqli_stmt_get_result($comando));
    mysqli_stmt_close($comando);
    return (int)$linha['total'] > 0;
}

function servicoExcluirDoEmpresario($conexao, $idservico, $idempresario) {
    $comando = mysqli_prepare($conexao, "DELETE FROM servicos WHERE idservicos = ? AND empresario_idempresario = ?");
    mysqli_stmt_bind_param($comando, 'ii', $idservico, $idempresario);
    mysqli_stmt_execute($comando);
    $apagou = mysqli_stmt_affected_rows($comando) > 0;
    mysqli_stmt_close($comando);
    return $apagou;
}

// Para o aviso "falta cadastrar..." do painel: ['servicos' => int, 'horarios' => int]
function painelContagens($conexao, $idempresario) {
    $contagens = [];
    foreach (['servicos' => 'servicos', 'horarios' => 'horarios'] as $chave => $tabela) {
        $comando = mysqli_prepare($conexao, "SELECT COUNT(*) AS total FROM $tabela WHERE empresario_idempresario = ?");
        mysqli_stmt_bind_param($comando, 'i', $idempresario);
        mysqli_stmt_execute($comando);
        $linha = mysqli_fetch_assoc(mysqli_stmt_get_result($comando));
        mysqli_stmt_close($comando);
        $contagens[$chave] = (int)$linha['total'];
    }
    return $contagens;
}


// ---------- catálogo e lista de clientes ----------

// Todos os serviços de todos os profissionais (página "Serviços disponíveis")
function catalogoListarServicos($conexao) {
    $sql = "SELECT s.idservicos, s.serv_nome, s.serv_valor, s.serv_descricao, s.serv_tempo, s.serv_fotos,
                   e.idempresario, u.user_nome AS profissional
            FROM servicos s
            JOIN empresario e ON e.idempresario = s.empresario_idempresario
            JOIN usuarios u ON u.idusuarios = e.usuarios_idusuarios
            ORDER BY u.user_nome, s.serv_nome";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $lista[] = $linha;
    }
    mysqli_stmt_close($comando);
    return $lista;
}

// Clientes que já agendaram com esse empresário (sem contar agendamentos cancelados).
// Só nome e telefone: é o que o empresário precisa para entrar em contato.
function agendaListarClientesDoEmpresario($conexao, $idempresario) {
    $sql = "SELECT u.idusuarios, u.user_nome, u.user_telefone, u.user_fotos,
                   COUNT(*) AS total, MAX(a.agend_horario) AS mais_recente
            FROM agenda a
            JOIN usuarios u ON u.idusuarios = a.agend_idusuarios
            WHERE a.agend_idempresario = ? AND a.agend_status <> 'cancelado'
            GROUP BY u.idusuarios, u.user_nome, u.user_telefone, u.user_fotos
            ORDER BY mais_recente DESC";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idempresario);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $lista[] = $linha;
    }
    mysqli_stmt_close($comando);
    return $lista;
}

//--------------------VENDAS-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------VENDAS-----------------------//


function listarvenda($conexao) {
    $sql = "SELECT * FROM tb_venda";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $listar_venda = [];
    while ($venda = mysqli_fetch_assoc($resultado)) {
        $listar_venda[] = $venda;
    }
    mysqli_stmt_close($comando);
    return $listar_venda;
}

function salvarVenda($conexao, $idcliente, $idproduto, $valor_total, $data ){
    $sql= "INSERT INTO tb_venda (idcliente, idproduto, valor_total, data) VALUES (?,?,?,?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'iids',$idcliente, $idproduto, $valor_total, $data);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};

//--------------------LOGIN-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------LOGIN-----------------------//

function verificartlogin(){
    return isset($_SESSION['usuario']);
}

function verificaradmin(){
    return (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'admin');
}

function verificarusuario(){
    return (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'usuario');
}

function logout(){
    session_destroy();
}

    //function login($conexao, $email, $senha) {
    //    $sql = "SELECT * FROM mydb.usuarios WHERE user_email = ? AND user_senha = ?";
    //    $stmt = $conexao ->prepare($sql);
    //    $stmt->bind_param("ss", $email, $senha);
    //    $stmt->execute();
//
    //    $resultado = $stmt->get_result();
    //    if ($resultado->num_rows > 0) {
    //        $usuario = $resultado->fetch_assoc();
    //        $_SESSION['usuario'] = $usuario['user_nome'];
    //        $_SESSION['id'] = $usuario['idusuarios'];
    //        return true;
    //    } 
    //        return false;
    //    }

    function login($conexao, $email, $senha) {
    $sql = "SELECT * FROM mydb.usuarios WHERE user_email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();                 //encontrar algum user com o email colocado

        if (password_verify($senha, $usuario['user_senha'])) {            //comparar a senha com o hash para permitir o login com a senha
            $_SESSION['usuario'] = $usuario['user_nome'];
            $_SESSION['id'] = $usuario['idusuarios'];
            return true;
        }
    }

    return false;
}

//--------------------IMAGEM-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------IMAGEM-----------------------//

        function uploadImg ($arquivo){
        $diretorio = '../imagens/';
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png'];

        if(!in_array($extensao, $permitidas)){ 
            return false;
        }

        if($arquivo['size']> 1024 * 1024 * 2){ // permite até 2MB
            return false;
        }

        $nomeArquivo = uniqid() . "_" . $arquivo['name'];
        $caminho = $diretorio . $nomeArquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $caminho)){
            return $caminho;
        }

        return false;
    }






//--------------------comentarios-----------------------//
// ⡤⠒⢤⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⡤⠒⢤
//⢣⡀⠀⠉⠲⢤⣀⡀⠀⠀⠀⠀⠀⠀⢀⣀⡤⠖⠉⠀⢀⡜
//⢸⡉⠒⠄⠀⠀⠀⢉⡙⢢⠀⠀⡔⢋⡉⠀⠀⠀⠠⠒⢉⡇
  //⠉⢖⠒⠀⠀⠀⣇⠀⣸⠀⠀⣇⠀⣸⠀⠀⠀⠒⡲⠉⠀
    //⠉⠙⠫⠤⠚⠉⠀⠀⠀⠀⠉⠓⠤⠝⠋⠉ 

//--------------------comentarios-----------------------//


function deletarcomentarios($conexao, $idcomentarios) {
    $sql = "DELETE FROM comentarios WHERE idcomentarios = ?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando, 'i', $idcomentarios);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou; //true ou false
};

function listarcomentarios($conexao) {
    $sql = "SELECT * FROM comentarios";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_execute($comando);
    $resultado = mysqli_stmt_get_result($comando);
    $lista_comentarios = [];
    while ($comentarios = mysqli_fetch_assoc($resultado)) {
        $lista_comentarios[] = $comentarios;
    }
    mysqli_stmt_close($comando);
    return $lista_comentarios;
};

function salvarcomentarios($conexao, $coment_texto, $comentarios_estrela) {
    $sql = "INSERT INTO comentarios (coment_texto, comentarios_estrela) VALUES (?, ?)";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando,'si', $coment_texto, $comentarios_estrela);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};

function editarcomentarios($conexao, $coment_texto, $coment_idusuarios, $comentarios_estrela,$idcomentarios) {
    $sql = "UPDATE comentarios SET texto=?, idusuarios=?, estrela=?, WHERE idcomentarios=?";
    $comando = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($comando,'sii', $coment_texto, $coment_idusuarios, $comentarios_estrela, $idcomentarios);
    $funcionou = mysqli_stmt_execute($comando);
    mysqli_stmt_close($comando);
    return $funcionou;
};


?>
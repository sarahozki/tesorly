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
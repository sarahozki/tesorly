<!-- 
⊹
⢠⡏⠉⠑⢄⠀ ⠀  ⡠⠋⠉⢱⡀
⡇⠙⠒⠒⠬⡗⢒⢮⠄⠒⠒⠁⢣
⠇⠀⠈⠁⢁⡷⠤⢮⠈⠁⠀⠀⡌
⠘⢄⣀⡰⢻⠁⠀⠘⡕⢄⣀⡰⠁⠀⊹   CADASTRO FUNCIONANDO (ARRUMAR O ADM) - SÓ FALTA O CSS
⠀⡎⠘⢀⠇⠀⠀⠀⢱⠈⠂⠡⠀
⠀⠑⢄⡜⠢⡀⠀⢀⠔⠇⡴⠃⠀
⠀⠀⠀⠑⠠⠚⠀⠓⠔⠋⠀⠀
⊹
-->

<?php

?>

<!DOCTYPE html>
<html lang="en" class="html-cadastro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Cadastro - Tesorly</title>
    <style>
        .invalido {
            color: red;
        }

        .valido {
            color: green;
        }
    </style>
</head>
<body class="body-cadastro">
        <h1 class="h1_cadastro">Cadastro</h1>

        <button class="butao_cadastro"><a href="../index.php">◀️</a></button>

        <img src="logo_tesorly.png" alt="Logo Tesorly" class="logotesorly_cadastro">

        <img src="../imagens/cabelo.png" alt="foto mulher" class="foto_cadastro">

    <form action="../saves/save_user.php" method="post" id="form" class="form_cadastro" enctype="multipart/form-data">

        <div class="form-content-cadastro">
            <label for="name" class="label-cadastro">Nome Completo:</label>
            <input type="text" id="name" name="nome" class="input-cadastro">
        </div>

        <div class="form-content-cadastro">
            <label for="email" class="label-cadastro">Email:</label>
            <input type="email" id="email" name="email" class="input-cadastro">
        </div>

        <div class="form-content-cadastro">
            <label for="telefone" class="label-cadastro">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" class="input-cadastro">
        </div>

        <div class="form-content-cadastro">
            <label for="prod_fotos" class="label-cadastro">Foto do Produto:</label>
            <input type="file" id="prod_fotos" name="prod_fotos" accept="image/*" class="input-cadastro" >
        </div>

        <div class="form-content-cadastro">
            <label for="senha" class="label-cadastro">Senha:</label>
            <input type="password" id="senha" name="senha" class="input-cadastro">
        </div>
        <ul>
        <li id="regra-quantidade" class="invalido">
            Minimo de 8 caracteres
        </li>

        <li id="regra-numero" class="invalido">
            Minimo de 1 algarismo
        </li>

        <li id="regra-maius" class="invalido">
            Minimo de 1 letra maiuscula
        </li>
    </ul>
        
        <div class="form-content-cadastro">
            <div class="mostrarSenha">
                <input type="checkbox" id="mostrar-senha" name="mostrar-senha" class="input-cadastro">
                <label for="mostrar-senha" class="label-cadastro">Mostrar Senha</label>
            </div>
        </div>
        

        <div>
            <input type="checkbox" id="sou_emp" name="sou_emp" class="input-cadastro">
            <label for="sou_emp" class="label-cadastro">Sou empresário</label>
        </div>

        <div class="registro-cadastro">
            <p class="login-cadastro">Já tem uma conta? <a href="../php/login.php">Faça login</a></p>
        </div>

        <input type="submit" value="Cadastrar" id="cadastro" class="submit-cadastro">
    </form>

    


    <!-- Script para redirecionar ao clicar no checkbox (TEM QUE TROCAR ISSO DAQUI)-->
    <script>
        const campoSenha = document.getElementById('senha');
        const quantidade = document.getElementById('regra-quantidade');
        const numero = document.getElementById('regra-numero');
        const letramaius = document.getElementById('regra-maius');

        document.getElementById('sou_emp').onclick = function() {
        const senha = document.getElementById('senha');
    const mostrarSenha = document.getElementById('mostrar-senha');

    mostrarSenha.addEventListener('click', function() {
        senha.type = mostrarSenha.checked ? 'text' : 'password';
    });
}
 function validar(regra, valor) {

            if (valor) {
                regra.classList.remove('invalido');
                regra.classList.add('valido');
            } else {
                regra.classList.remove('valido');
                regra.classList.add('invalido');
            }

        }

        campoSenha.addEventListener('input', () => {

            const tamanhoMinimo = campoSenha.value.length >= 8;
            const temnumero = /[0-9]/.test(campoSenha.value);
            const temmaius = /[A-Z]/.test(campoSenha.value);
            


            validar(quantidade, tamanhoMinimo);
            validar(numero, temnumero);
            validar(letramaius, temmaius);
            

        });


        // ===== Validação de email =====
        const campoEmail = document.getElementById('email');
        const formCadastro = document.getElementById('form');

        // cria a mensagem logo abaixo do campo de email
        const regraEmail = document.createElement('small');
        regraEmail.id = 'regra-email';
        campoEmail.insertAdjacentElement('afterend', regraEmail);

        campoEmail.addEventListener('input', () => {

            const emailvalido = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(campoEmail.value.trim());

            if (campoEmail.value === '') {
                regraEmail.textContent = '';
            } else {
                regraEmail.textContent = emailvalido ? 'válido' : 'Email inválido (ex: nome@dominio.com)';
            }

            validar(regraEmail, emailvalido);

        });

        // impede o envio se o email estiver inválido
        formCadastro.addEventListener('submit', (e) => {

            const emailvalido = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(campoEmail.value.trim());

            if (!emailvalido) {
                e.preventDefault();
                regraEmail.textContent = 'Informe um email válido (ex: nome@dominio.com)';
                validar(regraEmail, false);
                campoEmail.focus();
            }

        });


    </script>

</body>
</html>
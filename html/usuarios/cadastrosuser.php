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

        .body-cadastro {
            position: relative;
            min-height: 100vh;
            background-color: #d9c9f5;
            overflow-x: hidden;
            display: flex;
            align-items: center;
        }

        /* ===== Foto da mulher ===== */
        .foto_cadastro {
            width: 45%;
            max-width: 550px;
            height: 100vh;
            object-fit: cover;
            border-radius: 0 50% 50% 0 / 0 45% 45% 0;
            flex-shrink: 0;
        }


        /* ===== Botão de voltar (dentro do card, seta à direita) ===== */
        .butao_cadastro {
            position: absolute;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            top: 152px;
            right: calc(20% + 24px);
            z-index: 5;
        }

        .butao_cadastro a {
            text-decoration: none;
        }

        /* ===== Logo pequena (flor) ===== */
        .h1_cadastro {
            width: 20px;
            height: 30px;
            position: absolute;
            top: 70px;
            right: calc(16% + 300px);
            z-index: 5;
            color: black;
        }

        /* ===== Formulário (card) ===== */
        .form_cadastro {
            background-color: #e9defb;
            border: 1px solid #5c3d75;
            border-radius: 20px;
            width: 100%;
            max-width: 340px;
            padding: 40px 32px 32px;
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 125px;
            right: 20%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        
        /* ===== Cada bloco label + input ===== */
        .form-content-cadastro {
            display: flex;
            flex-direction: column;
            margin-bottom: 14px;
        }
        
        .label-cadastro {
            font-size: 12px;
            color: #333;
            margin-bottom: 6px;
            font-weight: 500;
        }
        
        /* ===== Inputs de texto ===== */
        .input-cadastro {
            background-color: #eeeeee;
            border: none;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 14px;
            color: #333;
            outline: none;
            width: 100%;
            transition: box-shadow 0.2s ease;
        }
        
        .input-cadastro:focus {
            box-shadow: 0 0 0 2px #5c3d75;
        }
        
        /* ===== Checkboxes ===== */
        input[type="checkbox"].input-cadastro {
            width: auto;
            accent-color: #5c1a4a;
            cursor: pointer;
        }
        
        .mostrarSenha {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .mostrarSenha .label-cadastro {
            margin-bottom: 0;
        }
        
        .form_cadastro > div:not(.form-content-cadastro):not(.registro-cadastro) {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 18px;
        }
        
        .form_cadastro > div:not(.form-content-cadastro):not(.registro-cadastro) .label-cadastro {
            margin-bottom: 0;
        }
        
        /* ===== Texto "Já tem uma conta?" ===== */
        .registro-cadastro {
            text-align: center;
            margin-bottom: 16px;
        }
        
        .login-cadastro {
            font-size: 13px;
            color: #333;
        }
        
        .login-cadastro a {
            color: #5c1a4a;
            font-weight: 700;
            text-decoration: none;
        }
        
        .login-cadastro a:hover {
            text-decoration: underline;
        }
        
        /* ===== Botão Cadastrar ===== */
        .submit-cadastro {
            background-color: #5c1a4a;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .submit-cadastro:hover {
            background-color: #74225c;
        }
        
        /* ===== Responsivo ===== */
        @media (max-width: 900px) {
            .foto_cadastro {
                display: none;
            }
        
            .body-cadastro {
                display: block;
            }
        
            .form_cadastro {
                position: static;
                margin: 100px auto 40px;
            }
        
            .h1_cadastro {
                position: static;
                text-align: center;
                margin-bottom: 20px;
            }
        
            .butao_cadastro,
            .logotesorly_cadastro {
                position: static;
                margin: 10px auto;
                display: block;
            }
        }
    </style>
</head>
<body class="body-cadastro">
        <h1 class="h1_cadastro">Cadastro</h1>

        <button class="butao_cadastro"><a href="../index.php">◀⬅</a></button>

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
            <label for="prod_fotos" class="label-cadastro">Foto de perfil:</label>
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
        const senha = document.getElementById('senha');
        const mostrarSenha = document.getElementById('mostrar-senha');

    mostrarSenha.addEventListener('click', function() {
        senha.type = mostrarSenha.checked ? 'text' : 'password';
    });

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
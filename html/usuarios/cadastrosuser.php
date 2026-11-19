<!DOCTYPE html>
<html lang="pt-br" class="html-cadastro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Cadastro - Tesorly</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .body-cadastro {
            position: relative;
            min-height: 100vh;
            background-color: #d9c9f5;
            overflow-x: hidden;
            display: flex;
            align-items: center;
        }

        /* ===== Foto lateral ===== */
        .foto_cadastro {
            width: 45%;
            max-width: 550px;
            height: 100vh;
            object-fit: cover;
            border-radius: 0 50% 50% 0 / 0 45% 45% 0;
            flex-shrink: 0;
        }

        /* ===== Botão de voltar ===== */
        .butao_cadastro {
            position: absolute;
            top: 36px;
            left: 36px;
            z-index: 10;
        }

        .butao_cadastro a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #5c1a4a;
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            transition: all 0.2s ease;
        }

        .butao_cadastro a:hover {
            background: #5c1a4a;
            color: #ffffff;
            transform: translateX(-3px);
        }

        /* ===== Card do Formulário ===== */
        .form_cadastro {
            background-color: #f7f3fd;
            border: 2px solid #5c3d75;
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            padding: 36px 32px 32px;
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 50%;
            right: 14%;
            transform: translateY(-50%);
            box-shadow: 0 12px 32px rgba(92, 26, 74, 0.15);
        }

        .titulo_card_cadastro {
            color: #5c1a4a;
            font-size: 1.8rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 6px;
        }

        .subtitulo_card_cadastro {
            color: #6d5b7e;
            font-size: 0.88rem;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Banner de Alerta do Formulário */
        .alerta_validacao_topo {
            display: none;
            background: #FDE8E8;
            border: 1px solid #F98080;
            color: #9B1C1C;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 16px;
            animation: fadeIn 0.3s ease;
        }

        /* ===== Bloco de cada Campo ===== */
        .form-content-cadastro {
            display: flex;
            flex-direction: column;
            margin-bottom: 14px;
            position: relative;
        }

        .label-cadastro {
            font-size: 0.85rem;
            color: #3b2348;
            margin-bottom: 5px;
            font-weight: 600;
        }

        /* ===== Inputs com feedback visual ===== */
        .input-cadastro {
            background-color: #ffffff;
            border: 1.5px solid #d4c5e8;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 0.92rem;
            color: #333;
            outline: none;
            width: 100%;
            transition: all 0.2s ease;
        }

        .input-cadastro:focus {
            border-color: #5c1a4a;
            box-shadow: 0 0 0 3px rgba(92, 26, 74, 0.15);
        }

        .input-cadastro.valido {
            border-color: #27AE60 !important;
            background-color: #F9FFF9;
        }

        .input-cadastro.invalido {
            border-color: #E74C3C !important;
            background-color: #FFF9F9;
        }

        /* Wrap de senha com botão de visualização */
        .senha_wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .senha_wrap input {
            padding-right: 42px;
        }

        .btn_toggle_senha {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.15rem;
            color: #7b658d;
            padding: 4px;
            line-height: 1;
            transition: color 0.2s ease;
        }

        .btn_toggle_senha:hover {
            color: #5c1a4a;
        }

        /* Mensagens de validação instantânea */
        .msg_campo {
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 4px;
            display: none;
            align-items: center;
            gap: 5px;
            animation: fadeIn 0.2s ease;
        }

        .msg_campo.ativa {
            display: inline-flex;
        }

        .msg_campo.erro {
            color: #C0392B;
        }

        .msg_campo.sucesso {
            color: #1E8449;
        }

        /* ===== Requisitos e Força da Senha ===== */
        .box_seguranca_senha {
            background: #ffffff;
            border: 1.5px solid #e5daf3;
            border-radius: 12px;
            padding: 12px 14px;
            margin: 6px 0 14px;
        }

        .topo_forca_senha {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .label_forca_titulo {
            font-size: 0.78rem;
            color: #5c3d75;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .status_forca_texto {
            font-size: 0.8rem;
            font-weight: 700;
        }

        /* Barra de Força da Senha */
        .trilha_forca_senha {
            height: 6px;
            background: #eee;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .preenchimento_forca_senha {
            height: 100%;
            width: 0%;
            border-radius: 999px;
            transition: all 0.3s ease;
        }

        /* Checklist estilizado de regras */
        .lista_regras_senha {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .item_regra_senha {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #887898;
            transition: color 0.2s ease;
        }

        .icone_regra {
            width: 17px;
            height: 17px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 900;
            background: #E8E2F2;
            color: #8A7E9C;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }

        /* Regra cumprida (verde com checkmark) */
        .item_regra_senha.valido {
            color: #1E8449;
        }

        .item_regra_senha.valido .icone_regra {
            background: #27AE60;
            color: #ffffff;
            box-shadow: 0 0 6px rgba(39, 174, 96, 0.4);
            transform: scale(1.1);
        }

        /* Regra inválida / pendente */
        .item_regra_senha.invalido {
            color: #C0392B;
        }

        .item_regra_senha.invalido .icone_regra {
            background: #FDEDED;
            color: #E74C3C;
        }

        /* ===== Checkbox Empresário ===== */
        .box_checkbox_emp {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 18px;
        }

        .box_checkbox_emp input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #5c1a4a;
            cursor: pointer;
        }

        .box_checkbox_emp label {
            font-size: 0.88rem;
            color: #3b2348;
            font-weight: 600;
            cursor: pointer;
        }

        /* ===== Rodapé de Login ===== */
        .registro-cadastro {
            text-align: center;
            margin-bottom: 16px;
        }

        .login-cadastro {
            font-size: 0.88rem;
            color: #4b3658;
        }

        .login-cadastro a {
            color: #5c1a4a;
            font-weight: 700;
            text-decoration: none;
        }

        .login-cadastro a:hover {
            text-decoration: underline;
        }

        /* ===== Botão Submit ===== */
        .submit-cadastro {
            background: linear-gradient(135deg, #5c1a4a 0%, #7d2666 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(92, 26, 74, 0.25);
            transition: all 0.2s ease;
        }

        .submit-cadastro:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(92, 26, 74, 0.35);
        }

        .submit-cadastro:active {
            transform: translateY(0);
        }

        /* Animação de tremor para erro de envio */
        @keyframes shakeForm {
            0%, 100% { transform: translateY(-50%) translateX(0); }
            20%, 60% { transform: translateY(-50%) translateX(-8px); }
            40%, 80% { transform: translateY(-50%) translateX(8px); }
        }

        .form_shake {
            animation: shakeForm 0.45s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-3px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsividade */
        @media (max-width: 960px) {
            .foto_cadastro {
                display: none;
            }
            .body-cadastro {
                display: flex;
                justify-content: center;
                padding: 80px 20px 40px;
            }
            .form_cadastro {
                position: static;
                transform: none;
                margin: 0 auto;
            }
            @keyframes shakeForm {
                0%, 100% { transform: translateX(0); }
                20%, 60% { transform: translateX(-8px); }
                40%, 80% { transform: translateX(8px); }
            }
        }
    </style>
</head>
<body class="body-cadastro">

    <div class="butao_cadastro">
        <a href="../index.php">← Voltar ao início</a>
    </div>

    <img src="../imagens/cabelo.png" alt="Foto salão" class="foto_cadastro">

    <form action="../saves/save_user.php" method="post" id="form" class="form_cadastro" enctype="multipart/form-data" novalidate>
        <h1 class="titulo_card_cadastro">Criar Conta</h1>
        <p class="subtitulo_card_cadastro">Junte-se à comunidade Tesorly</p>

        <!-- Banner de erro caso tente enviar inválido -->
        <div id="alerta_erro_topo" class="alerta_validacao_topo" role="alert"></div>

        <!-- Campo Nome Completo -->
        <div class="form-content-cadastro">
            <label for="name" class="label-cadastro">Nome Completo:</label>
            <input type="text" id="name" name="nome" class="input-cadastro" placeholder="Ex: Maria Silva" required autocomplete="name">
            <span id="msg_nome" class="msg_campo"></span>
        </div>

        <!-- Campo Email -->
        <div class="form-content-cadastro">
            <label for="email" class="label-cadastro">Email:</label>
            <input type="email" id="email" name="email" class="input-cadastro" placeholder="Ex: maria@email.com" required autocomplete="email">
            <span id="msg_email" class="msg_campo"></span>
        </div>

        <!-- Campo Telefone com Máscara Dinâmica -->
        <div class="form-content-cadastro">
            <label for="telefone" class="label-cadastro">Telefone / WhatsApp:</label>
            <input type="tel" id="telefone" name="telefone" maxlength="15" class="input-cadastro" placeholder="(11) 98765-4321" required autocomplete="tel">
            <span id="msg_telefone" class="msg_campo"></span>
        </div>

        <!-- Campo Foto de perfil -->
        <div class="form-content-cadastro">
            <label for="prod_fotos" class="label-cadastro">Foto de perfil (opcional):</label>
            <input type="file" id="prod_fotos" name="prod_fotos" accept="image/jpeg,image/png,image/webp" class="input-cadastro">
        </div>

        <!-- Campo Senha com botão Ver/Ocultar -->
        <div class="form-content-cadastro" style="margin-bottom: 6px;">
            <label for="senha" class="label-cadastro">Senha:</label>
            <div class="senha_wrap">
                <input type="password" id="senha" name="senha" class="input-cadastro" placeholder="Crie uma senha forte" required autocomplete="new-password">
                <button type="button" id="btn_toggle_senha" class="btn_toggle_senha" aria-label="Mostrar ou ocultar senha">👁</button>
            </div>
        </div>

        <!-- Caixa estilizada com força e requisitos da senha -->
        <div class="box_seguranca_senha">
            <div class="topo_forca_senha">
                <span class="label_forca_titulo">Força da senha:</span>
                <span id="txt_forca_senha" class="status_forca_texto" style="color: #999;">Digite a senha</span>
            </div>
            
            <div class="trilha_forca_senha">
                <div id="barra_forca_senha" class="preenchimento_forca_senha"></div>
            </div>

            <ul class="lista_regras_senha">
                <li id="regra-quantidade" class="item_regra_senha">
                    <span class="icone_regra">✕</span>
                    <span>Mínimo de 8 caracteres</span>
                </li>
                <li id="regra-numero" class="item_regra_senha">
                    <span class="icone_regra">✕</span>
                    <span>Pelo menos 1 número (0 a 9)</span>
                </li>
                <li id="regra-maius" class="item_regra_senha">
                    <span class="icone_regra">✕</span>
                    <span>Pelo menos 1 letra maiúscula (A a Z)</span>
                </li>
            </ul>
        </div>

        <!-- Checkbox Sou empresário -->
        <div class="box_checkbox_emp">
            <input type="checkbox" id="sou_emp" name="sou_emp">
            <label for="sou_emp">Quero cadastrar como Profissional / Salão</label>
        </div>

        <div class="registro-cadastro">
            <p class="login-cadastro">Já tem uma conta? <a href="../login.php">Faça login</a></p>
        </div>

        <input type="submit" value="Finalizar Cadastro" id="cadastro" class="submit-cadastro">
    </form>

    <!-- JavaScript Avançado de Validação em Tempo Real -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('form');
        const alertaTopo = document.getElementById('alerta_erro_topo');

        const campoNome = document.getElementById('name');
        const msgNome = document.getElementById('msg_nome');

        const campoEmail = document.getElementById('email');
        const msgEmail = document.getElementById('msg_email');

        const campoTel = document.getElementById('telefone');
        const msgTel = document.getElementById('msg_telefone');

        const campoSenha = document.getElementById('senha');
        const btnToggleSenha = document.getElementById('btn_toggle_senha');
        const barraForca = document.getElementById('barra_forca_senha');
        const txtForca = document.getElementById('txt_forca_senha');

        const regraQtd = document.getElementById('regra-quantidade');
        const regraNum = document.getElementById('regra-numero');
        const regraMaius = document.getElementById('regra-maius');

        // ==========================================
        // 1. Alternar visualização da senha
        // ==========================================
        btnToggleSenha.addEventListener('click', function () {
            const ehPassword = campoSenha.type === 'password';
            campoSenha.type = ehPassword ? 'text' : 'password';
            btnToggleSenha.textContent = ehPassword ? '🔒' : '👁';
        });

        // ==========================================
        // 2. Validação da Senha e Barra de Força
        // ==========================================
        function validarRegraItem(el, valida) {
            const icone = el.querySelector('.icone_regra');
            if (valida) {
                el.classList.remove('invalido');
                el.classList.add('valido');
                icone.textContent = '✓';
            } else {
                el.classList.remove('valido');
                el.classList.add('invalido');
                icone.textContent = '✕';
            }
        }

        function avaliarSenha() {
            const val = campoSenha.value;
            const tem8 = val.length >= 8;
            const temNum = /[0-9]/.test(val);
            const temMaius = /[A-Z]/.test(val);

            validarRegraItem(regraQtd, tem8);
            validarRegraItem(regraNum, temNum);
            validarRegraItem(regraMaius, temMaius);

            const pontuacao = (tem8 ? 1 : 0) + (temNum ? 1 : 0) + (temMaius ? 1 : 0);

            if (val.length === 0) {
                barraForca.style.width = '0%';
                txtForca.textContent = 'Digite a senha';
                txtForca.style.color = '#999';
                campoSenha.classList.remove('valido', 'invalido');
            } else if (pontuacao === 1) {
                barraForca.style.width = '33%';
                barraForca.style.backgroundColor = '#E74C3C';
                txtForca.textContent = 'Fraca';
                txtForca.style.color = '#E74C3C';
                campoSenha.classList.remove('valido');
                campoSenha.classList.add('invalido');
            } else if (pontuacao === 2) {
                barraForca.style.width = '66%';
                barraForca.style.backgroundColor = '#F39C12';
                txtForca.textContent = 'Média';
                txtForca.style.color = '#F39C12';
                campoSenha.classList.remove('valido');
                campoSenha.classList.add('invalido');
            } else if (pontuacao === 3) {
                barraForca.style.width = '100%';
                barraForca.style.backgroundColor = '#27AE60';
                txtForca.textContent = 'Forte';
                txtForca.style.color = '#27AE60';
                campoSenha.classList.remove('invalido');
                campoSenha.classList.add('valido');
            }

            return pontuacao === 3;
        }

        campoSenha.addEventListener('input', avaliarSenha);

        // ==========================================
        // 3. Validação de Email
        // ==========================================
        function validarEmail() {
            const val = campoEmail.value.trim();
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
            const ehValido = regexEmail.test(val);

            if (val === '') {
                msgEmail.className = 'msg_campo';
                msgEmail.textContent = '';
                campoEmail.classList.remove('valido', 'invalido');
                return false;
            }

            if (ehValido) {
                msgEmail.className = 'msg_campo ativa sucesso';
                msgEmail.textContent = '✓ E-mail válido';
                campoEmail.classList.remove('invalido');
                campoEmail.classList.add('valido');
                return true;
            } else {
                msgEmail.className = 'msg_campo ativa erro';
                msgEmail.textContent = '✕ Digite um e-mail válido (ex: seu@email.com)';
                campoEmail.classList.remove('valido');
                campoEmail.classList.add('invalido');
                return false;
            }
        }

        campoEmail.addEventListener('input', validarEmail);

        // ==========================================
        // 4. Validação de Nome Completo
        // ==========================================
        function validarNome() {
            const val = campoNome.value.trim();
            const partes = val.split(/\s+/).filter(p => p.length > 0);
            const ehValido = (partes.length >= 2 && val.length >= 5);

            if (val === '') {
                msgNome.className = 'msg_campo';
                msgNome.textContent = '';
                campoNome.classList.remove('valido', 'invalido');
                return false;
            }

            if (ehValido) {
                msgNome.className = 'msg_campo ativa sucesso';
                msgNome.textContent = '✓ Nome válido';
                campoNome.classList.remove('invalido');
                campoNome.classList.add('valido');
                return true;
            } else {
                msgNome.className = 'msg_campo ativa erro';
                msgNome.textContent = '✕ Digite seu nome e sobrenome';
                campoNome.classList.remove('valido');
                campoNome.classList.add('invalido');
                return false;
            }
        }

        campoNome.addEventListener('input', validarNome);

        // ==========================================
        // 5. Máscara e Validação de Telefone
        // ==========================================
        function aplicarMascaraTelefone(e) {
            let val = e.target.value.replace(/\D/g, '');
            if (val.length > 11) val = val.substring(0, 11);

            if (val.length > 10) {
                // (11) 98765-4321
                val = val.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
            } else if (val.length > 5) {
                // (11) 9876-5432
                val = val.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
            } else if (val.length > 2) {
                val = val.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
            } else if (val.length > 0) {
                val = val.replace(/^(\d*)$/, '($1');
            }

            e.target.value = val;
            validarTelefone();
        }

        function validarTelefone() {
            const numeros = campoTel.value.replace(/\D/g, '');
            const ehValido = (numeros.length >= 10 && numeros.length <= 11);

            if (campoTel.value.trim() === '') {
                msgTel.className = 'msg_campo';
                msgTel.textContent = '';
                campoTel.classList.remove('valido', 'invalido');
                return false;
            }

            if (ehValido) {
                msgTel.className = 'msg_campo ativa sucesso';
                msgTel.textContent = '✓ Telefone válido';
                campoTel.classList.remove('invalido');
                campoTel.classList.add('valido');
                return true;
            } else {
                msgTel.className = 'msg_campo ativa erro';
                msgTel.textContent = '✕ Digite o DDD e número (10 ou 11 dígitos)';
                campoTel.classList.remove('valido');
                campoTel.classList.add('invalido');
                return false;
            }
        }

        campoTel.addEventListener('input', aplicarMascaraTelefone);

        // ==========================================
        // 6. Validação no Envio (Submit) com Tremor
        // ==========================================
        form.addEventListener('submit', function (e) {
            const nomeOk = validarNome();
            const emailOk = validarEmail();
            const telOk = validarTelefone();
            const senhaOk = avaliarSenha();

            if (!nomeOk || !emailOk || !telOk || !senhaOk) {
                e.preventDefault();

                // Dispara efeito de shake no formulário
                form.classList.remove('form_shake');
                void form.offsetWidth; // Força reflow
                form.classList.add('form_shake');

                // Exibe alerta visual no topo do card
                let motivos = [];
                if (!nomeOk) motivos.push('Nome completo (nome e sobrenome)');
                if (!emailOk) motivos.push('E-mail válido');
                if (!telOk) motivos.push('Telefone de contato');
                if (!senhaOk) motivos.push('Senha atendendo todos os 3 requisitos');

                alertaTopo.style.display = 'block';
                alertaTopo.textContent = 'Corrija os seguintes campos: ' + motivos.join(', ') + '.';

                // Foco no primeiro campo com erro
                if (!nomeOk) campoNome.focus();
                else if (!emailOk) campoEmail.focus();
                else if (!telOk) campoTel.focus();
                else if (!senhaOk) campoSenha.focus();
            } else {
                alertaTopo.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>

// Painel do empresário: confirmação ao excluir e comportamento do formulário de horários.
(function () {
    'use strict';

    function iniciar() {
        // pergunta antes de enviar formulários marcados com data-confirmar (ex.: excluir serviço)
        document.querySelectorAll('form[data-confirmar]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (!window.confirm(form.dataset.confirmar)) {
                    e.preventDefault();
                }
            });
        });

        var form = document.getElementById('form_horarios');
        if (!form) { return; }

        function campos(linha) {
            return linha.querySelectorAll('input[type="time"]');
        }

        // dia desmarcado = campos desativados (e não enviados); marcado = turno 1 obrigatório
        function aplicar(linha) {
            var ativo = linha.querySelector('input[type="checkbox"]').checked;
            var horas = campos(linha);
            linha.classList.toggle('inativo_painel', !ativo);
            horas.forEach(function (campo, i) {
                campo.disabled = !ativo;
                campo.required = ativo && i < 2; // só o turno 1 é obrigatório
            });
        }

        var linhas = form.querySelectorAll('tr[data-dia]');
        linhas.forEach(function (linha) {
            aplicar(linha);
            linha.querySelector('input[type="checkbox"]').addEventListener('change', function () {
                aplicar(linha);
            });
        });

        // copia os horários da segunda para terça a sexta e marca esses dias
        var botao = document.getElementById('copiar_segunda');
        if (botao) {
            botao.addEventListener('click', function () {
                var segunda = form.querySelector('tr[data-dia="seg"]');
                if (!segunda) { return; }
                var origem = campos(segunda);
                ['ter', 'qua', 'qui', 'sex'].forEach(function (codigo) {
                    var linha = form.querySelector('tr[data-dia="' + codigo + '"]');
                    if (!linha) { return; }
                    linha.querySelector('input[type="checkbox"]').checked = true;
                    aplicar(linha);
                    campos(linha).forEach(function (campo, i) { campo.value = origem[i].value; });
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar);
    } else {
        iniciar();
    }
})();

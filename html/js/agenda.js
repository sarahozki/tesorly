// Agenda: marcação no perfil do profissional + ações (confirmar/cancelar) na lista.
(function () {
    'use strict';

    // ---------- perfil do profissional: escolher serviço, dia e horário ----------
    function iniciarMarcacao(raiz) {
        var idempresario = raiz.dataset.idempresario;
        var selServico = raiz.querySelector('#agenda_servico');
        var botoesDia = raiz.querySelectorAll('.dia_btn_agenda');
        var grade = raiz.querySelector('#agenda_horarios');
        var resumo = raiz.querySelector('#agenda_resumo');
        var botao = raiz.querySelector('#agenda_confirmar');
        var msg = raiz.querySelector('#agenda_msg');

        var estado = { data: null, hora: null };
        var contador = 0; // ignora respostas antigas se o cliente clicar rápido

        function mostrarMsg(texto, ok) {
            msg.textContent = texto || '';
            msg.className = 'msg_agenda' + (texto ? (ok ? ' ok' : ' erro') : '');
        }

        function aviso(texto) {
            grade.textContent = '';
            var p = document.createElement('p');
            p.className = 'aviso_agenda';
            p.textContent = texto;
            grade.appendChild(p);
        }

        function atualizarResumo() {
            if (estado.data && estado.hora) {
                var partes = estado.data.split('-');
                resumo.textContent = partes[2] + '/' + partes[1] + ' às ' + estado.hora + ' — ' +
                    selServico.options[selServico.selectedIndex].textContent.trim();
            } else {
                resumo.textContent = '';
            }
            botao.disabled = !(estado.data && estado.hora);
        }

        function desenhar(horarios) {
            if (!horarios.length) {
                estado.hora = null;
                aviso('Sem horários neste dia.');
                atualizarResumo();
                return;
            }

            var horaAnterior = estado.hora;
            var aindaLivre = false;
            grade.textContent = '';

            horarios.forEach(function (h) {
                var chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'chip_horario_agenda';
                chip.textContent = h.hora;

                if (!h.livre) {
                    chip.disabled = true;
                    chip.title = h.motivo === 'encerra'
                        ? 'O serviço não cabe antes do fim do expediente'
                        : 'Horário ocupado';
                } else {
                    if (h.hora === horaAnterior) {
                        chip.classList.add('selecionado');
                        aindaLivre = true;
                    }
                    chip.addEventListener('click', function () {
                        estado.hora = h.hora;
                        grade.querySelectorAll('.chip_horario_agenda').forEach(function (c) {
                            c.classList.remove('selecionado');
                        });
                        chip.classList.add('selecionado');
                        mostrarMsg('');
                        atualizarResumo();
                    });
                }
                grade.appendChild(chip);
            });

            if (horaAnterior && !aindaLivre) {
                mostrarMsg('O horário ' + horaAnterior + ' acabou de ser ocupado. Escolha outro.', false);
                estado.hora = null;
            }
            atualizarResumo();
        }

        function carregar() {
            if (!estado.data || !selServico.value) {
                return;
            }
            var minha = ++contador;
            var params = new URLSearchParams({
                idempresario: idempresario,
                idservico: selServico.value,
                data: estado.data
            });

            fetch('../api/horarios.php?' + params.toString(), { credentials: 'same-origin' })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res) {
                    if (minha !== contador) { return; }
                    if (!res.ok) {
                        aviso(res.d.erro || 'Erro ao buscar horários.');
                        return;
                    }
                    desenhar(res.d.horarios);
                })
                .catch(function () {
                    if (minha === contador) { aviso('Erro de conexão ao buscar horários.'); }
                });
        }

        botoesDia.forEach(function (btn) {
            btn.addEventListener('click', function () {
                botoesDia.forEach(function (b) { b.classList.remove('selecionado'); });
                btn.classList.add('selecionado');
                estado.data = btn.dataset.data;
                estado.hora = null;
                mostrarMsg('');
                atualizarResumo();
                aviso('Buscando horários...');
                carregar();
            });
        });

        selServico.addEventListener('change', function () {
            estado.hora = null; // a duração muda quais horários cabem
            mostrarMsg('');
            atualizarResumo();
            carregar();
        });

        botao.addEventListener('click', function () {
            if (!estado.data || !estado.hora) { return; }
            botao.disabled = true;
            mostrarMsg('');

            fetch('../api/criar_agendamento.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    idempresario: Number(idempresario),
                    idservico: Number(selServico.value),
                    data: estado.data,
                    hora: estado.hora
                })
            })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res) {
                    mostrarMsg(res.d.mensagem || 'Erro ao agendar.', res.ok);
                    estado.hora = null; // o horário recém-marcado (ou perdido) sai da seleção
                    atualizarResumo();
                    carregar();         // redesenha: agora ele aparece ocupado
                })
                .catch(function () {
                    mostrarMsg('Erro de conexão. Tente novamente.', false);
                    atualizarResumo();
                });
        });

        // atualiza sozinho para mostrar horários que outros clientes acabaram de marcar
        setInterval(function () {
            if (!document.hidden) { carregar(); }
        }, 30000);

        // já abre no primeiro dia em que o profissional atende
        var primeiro = Array.prototype.find.call(botoesDia, function (b) { return !b.disabled; });
        if (primeiro) {
            primeiro.click();
        } else {
            aviso('Nenhum dia disponível nos próximos dias.');
        }
    }

    // ---------- lista de agendamentos: confirmar / cancelar ----------
    function iniciarLista() {
        document.querySelectorAll('.btn_status_agenda').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var acao = btn.dataset.acao;
                if (acao === 'cancelar' && !window.confirm('Cancelar este agendamento?')) { return; }

                btn.disabled = true;
                fetch('../api/gerenciar_agendamento.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ idagenda: Number(btn.dataset.id), acao: acao })
                })
                    .then(function (r) { return r.json(); })
                    .then(function (d) {
                        if (d.sucesso) {
                            window.location.reload();
                        } else {
                            window.alert(d.mensagem || 'Não foi possível concluir.');
                            btn.disabled = false;
                        }
                    })
                    .catch(function () {
                        window.alert('Erro de conexão. Tente novamente.');
                        btn.disabled = false;
                    });
            });
        });
    }

    function iniciar() {
        var raiz = document.getElementById('agenda_prof');
        if (raiz) { iniciarMarcacao(raiz); }
        iniciarLista();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar);
    } else {
        iniciar();
    }
})();

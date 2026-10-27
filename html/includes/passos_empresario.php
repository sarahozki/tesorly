<?php
// Aviso "o que falta" para o empresário começar a receber agendamentos.
// Usa $conexao e $meuIdEmpresario (definidos em app/index.php).
$contagens    = painelContagens($conexao, $meuIdEmpresario);
$faltaHorario = ($contagens['horarios'] === 0);
$faltaServico = ($contagens['servicos'] === 0);
?>
<?php if ($faltaHorario || $faltaServico): ?>
    <div class="passos_painel" role="note">
        <strong>Para os clientes conseguirem agendar com você, falta:</strong>
        <ol>
            <li class="<?= $faltaHorario ? 'pendente' : 'feito' ?>">
                <a href="?pagina=meus_horarios">Definir seus horários de atendimento</a>
            </li>
            <li class="<?= $faltaServico ? 'pendente' : 'feito' ?>">
                <a href="?pagina=meus_servicos">Cadastrar pelo menos um serviço</a>
            </li>
        </ol>
    </div>
<?php endif; ?>

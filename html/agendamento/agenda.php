
<?php
require_once __DIR__ . ("/../conexao.php");


/**
 * Classe responsável por toda a lógica de agendamento:
 * calcular horários livres, criar, cancelar e listar agendamentos.
 */
class Agenda
{
    private mysqli $conn;

    private const DIAS_SEMANA = [
        0 => 'domingo',
        1 => 'segunda',
        2 => 'terca',
        3 => 'quarta',
        4 => 'quinta',
        5 => 'sexta',
        6 => 'sabado',
    ];

    public function __construct(?mysqli $conn = null)
    {
        $this->conn = $conn ?? getConexao();
    }

    /**
     * Retorna os horários livres de um empresário, em uma data específica,
     * considerando a duração do serviço escolhido.
     *
     * @param int    $idEmpresario
     * @param string $data         formato 'Y-m-d'
     * @param int    $idServico
     * @return array lista de horários disponíveis (formato 'H:i')
     */
    public function getHorariosDisponiveis(int $idEmpresario, string $data, int $idServico): array
    {
        $diaSemana = self::DIAS_SEMANA[(int)date('w', strtotime($data))];

        // 1) Busca o expediente do empresário nesse dia da semana
        $stmt = $this->conn->prepare(
            'SELECT horarios_hora_inicio, horarios_hora_fim
             FROM horarios
             WHERE empresario_idempresario = ? AND horarios_dia_semana = ?'
        );
        $stmt->bind_param('is', $idEmpresario, $diaSemana);
        $stmt->execute();
        $expediente = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$expediente) {
            return []; // empresário não trabalha nesse dia
        }

        // 2) Busca a duração do serviço escolhido
        $stmt = $this->conn->prepare('SELECT serv_tempo FROM servicos WHERE idservicos = ?');
        $stmt->bind_param('i', $idServico);
        $stmt->execute();
        $servico = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$servico) {
            return []; // serviço não existe
        }

        $duracaoSegundos = $this->horaParaSegundos($servico['serv_tempo']);

        // 3) Busca agendamentos já existentes nesse dia (não cancelados)
        $stmt = $this->conn->prepare(
            "SELECT a.agend_horario, s.serv_tempo
             FROM agenda a
             JOIN servicos s ON s.idservicos = a.agend_idservicos
             WHERE a.agend_idempresario = ?
               AND DATE(a.agend_horario) = ?
               AND a.agend_status != 'cancelado'"
        );
        $stmt->bind_param('is', $idEmpresario, $data);
        $stmt->execute();
        $ocupados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Monta intervalos ocupados em segundos (desde 00:00)
        $intervalosOcupados = [];
        foreach ($ocupados as $ag) {
            $inicio = strtotime(date('H:i:s', strtotime($ag['agend_horario'])));
            $fim = $inicio + $this->horaParaSegundos($ag['serv_tempo']);
            $intervalosOcupados[] = [$inicio, $fim];
        }

        // 4) Gera os slots dentro do expediente, pulando os ocupados
        $inicioExpediente = strtotime($expediente['horarios_hora_inicio']);
        $fimExpediente = strtotime($expediente['horarios_hora_fim']);

        $disponiveis = [];
        for ($slot = $inicioExpediente; $slot + $duracaoSegundos <= $fimExpediente; $slot += $duracaoSegundos) {
            $slotFim = $slot + $duracaoSegundos;
            $livre = true;

            foreach ($intervalosOcupados as [$ocupInicio, $ocupFim]) {
                if ($slot < $ocupFim && $slotFim > $ocupInicio) {
                    $livre = false;
                    break;
                }
            }

            // Se a data for hoje, não mostra horário que já passou
            if ($livre && $data === date('Y-m-d') && $slot <= time() - strtotime(date('Y-m-d'))) {
                $livre = false;
            }

            if ($livre) {
                $disponiveis[] = date('H:i', $slot);
            }
        }

        return $disponiveis;
    }

    /**
     * Cria um novo agendamento, validando se o horário ainda está livre.
     * Retorna o id do agendamento criado, ou lança Exception se o horário
     * não estiver mais disponível (evita agendamento duplicado/conflito).
     */
    public function criarAgendamento(int $idUsuario, int $idEmpresario, int $idServico, string $dataHora): int
    {
        $data = date('Y-m-d', strtotime($dataHora));
        $hora = date('H:i', strtotime($dataHora));

        $livres = $this->getHorariosDisponiveis($idEmpresario, $data, $idServico);

        if (!in_array($hora, $livres, true)) {
            throw new Exception('Esse horário não está mais disponível. Escolha outro.');
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO agenda (agend_horario, agend_dia, agend_idusuarios)
             VALUES (?, 'pendente', ?)"
        );
        $dataHoraFormatada = date('Y-m-d H:i:s', strtotime($dataHora));
        $stmt->bind_param('siii', $dataHoraFormatada, $idUsuario, $idEmpresario, $idServico);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    /**
     * Cancela um agendamento (soft cancel — mantém o histórico).
     */
    public function cancelarAgendamento(int $idAgenda): bool
    {
        $stmt = $this->conn->prepare("UPDATE agenda SET agend_dia = 'cancelado' WHERE idagenda = ?");
        $stmt->bind_param('i', $idAgenda);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Confirma um agendamento pendente.
     */
    public function confirmarAgendamento(int $idAgenda): bool
    {
        $stmt = $this->conn->prepare("UPDATE agenda SET agend_dia = 'confirmado' WHERE idagenda = ?");
        $stmt->bind_param('i', $idAgenda);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Lista os agendamentos de um empresário em uma data específica.
     */
    public function listarPorEmpresarioEData(int $idEmpresario, string $data): array
    {
        $stmt = $this->conn->prepare(
            "SELECT a.idagenda, a.agend_horario, a.agend_dia,
                    u.user_nome, u.user_telefone,
                    s.serv_nome, s.serv_valor, s.serv_tempo
             FROM agenda a
             JOIN usuarios u ON u.idusuarios = a.agend_idusuarios
             JOIN servicos s ON s.idservicos = a.agend_idservicos
             WHERE a.agend_idempresario = ? AND DATE(a.agend_horario) = ?
             ORDER BY a.agend_horario ASC"
        );
        $stmt->bind_param('is', $idEmpresario, $data);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $resultado;
    }

    /**
     * Lista os agendamentos de um cliente (histórico / próximos).
     */
    public function listarPorUsuario(int $idUsuario): array
    {
        $stmt = $this->conn->prepare(
            "SELECT a.idagenda, a.agend_horario, a.agend_dia,
                    e.empre_tipo, e.empre_cidade,
                    s.serv_nome, s.serv_valor
             FROM agenda a
             JOIN empresario e ON e.idempresario = a.agend_idempresario
             JOIN servicos s ON s.idservicos = a.agend_idservicos
             WHERE a.agend_idusuarios = ?
             ORDER BY a.agend_horario DESC"
        );
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $resultado;
    }

    private function horaParaSegundos(string $hora): int
    {
        [$h, $m, $s] = array_map('intval', explode(':', $hora));
        return $h * 3600 + $m * 60 + $s;
    }
}
?>

<style>
  .agenda-embutida {
    --cor-fundo: #1F3A34;
    --cor-fundo-alt: #17302B;
    --cor-superficie: #2A4A42;
    --cor-superficie-2: #23413A;
    --cor-texto: #F3ECE2;
    --cor-texto-suave: #B9A78F;
    --cor-acento: #D89B4A;
    --cor-acento-hover: #E8B15F;
    --cor-acento-texto: #3A2A0F;
    --cor-linha: rgba(243, 236, 226, 0.14);
    --cor-selecionado-bg: rgba(216, 155, 74, 0.16);
    --cor-erro: #E8927A;

    font-family: 'Work Sans', sans-serif;
  }

  .agenda-embutida .ficha {
    background: var(--cor-superficie);
    color: var(--cor-texto);
    border-radius: 18px;
    max-width: 640px;
    margin: 0 auto;
    box-shadow: 0 24px 60px -24px rgba(0,0,0,0.5);
  }

  .agenda-embutida .etapa { padding: 26px 28px; }

  .agenda-embutida .rotulo-etapa {
    display: flex;
    align-items: baseline;
    gap: 10px;
    margin: 0 0 14px;
  }

  .agenda-embutida .rotulo-etapa .numero {
    font-family: monospace;
    font-size: 12px;
    color: var(--cor-acento);
    border: 1px solid var(--cor-acento);
    border-radius: 20px;
    padding: 2px 9px;
  }

  .agenda-embutida .rotulo-etapa .titulo {
    font-size: 15px;
    font-weight: 500;
    margin: 0;
  }

  .agenda-embutida select,
  .agenda-embutida input[type="date"] {
    width: 100%;
    background: var(--cor-superficie-2);
    border: 1px solid transparent;
    border-radius: 10px;
    padding: 10px 12px;
    color: var(--cor-texto);
    font-size: 14px;
  }

  .agenda-embutida .grade-horarios {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 14px;
  }

  .agenda-embutida .chip-horario {
    background: var(--cor-superficie-2);
    border: 1px solid transparent;
    border-radius: 8px;
    padding: 9px 0;
    font-family: monospace;
    font-size: 13px;
    color: var(--cor-texto);
    cursor: pointer;
  }

  .agenda-embutida .chip-horario.selecionado {
    background: var(--cor-acento);
    border-color: var(--cor-acento);
    color: var(--cor-acento-texto);
    font-weight: 600;
  }

  .agenda-embutida .aviso-vazio {
    color: var(--cor-texto-suave);
    font-size: 13px;
    grid-column: 1 / -1;
  }

  .agenda-embutida .canhoto {
    background: var(--cor-fundo-alt);
    border-radius: 0 0 18px 18px;
    padding: 22px 28px 26px;
  }

  .agenda-embutida .botao-confirmar {
    width: 100%;
    background: var(--cor-acento);
    color: var(--cor-acento-texto);
    border: none;
    border-radius: 10px;
    padding: 15px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 10px;
  }

  .agenda-embutida .botao-confirmar:hover { background: var(--cor-acento-hover); }
  .agenda-embutida .botao-confirmar:disabled { opacity: 0.5; cursor: not-allowed; }

  .agenda-embutida .mensagem-erro {
    color: var(--cor-erro);
    font-size: 13px;
    margin-top: 10px;
    text-align: center;
  }

  .agenda-embutida .mensagem-sucesso {
    color: #9BE38A;
    font-size: 14px;
    margin-top: 10px;
    text-align: center;
  }
</style>

<div class="agenda-embutida">
  <div class="ficha" id="ficha">

    <div class="etapa">
      <div class="rotulo-etapa"><span class="numero">01</span><p class="titulo">Escolha o profissional</p></div>
      <select id="select-empresario">
        <option value="">Selecione...</option>
      </select>
    </div>

    <div class="etapa">
      <div class="rotulo-etapa"><span class="numero">02</span><p class="titulo">Escolha o serviço</p></div>
      <select id="select-servico">
        <option value="">Selecione...</option>
      </select>
    </div>

    <div class="etapa">
      <div class="rotulo-etapa"><span class="numero">03</span><p class="titulo">Escolha a data</p></div>
      <input type="date" id="input-data">
    </div>

    <div class="etapa">
      <div class="rotulo-etapa"><span class="numero">04</span><p class="titulo">Escolha o horário</p></div>
      <div class="grade-horarios" id="lista-horarios">
        <p class="aviso-vazio">Selecione profissional, serviço e data primeiro.</p>
      </div>
    </div>

    <div class="canhoto">
      <button class="botao-confirmar" id="botao-confirmar" disabled>Confirmar agendamento</button>
      <p class="mensagem-erro" id="mensagem-erro" style="display:none;"></p>
      <p class="mensagem-sucesso" id="mensagem-sucesso" style="display:none;"></p>
    </div>

  </div>
</div>
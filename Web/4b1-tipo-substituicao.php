<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/api/config.php';

$regiao = $_GET['regiao'] ?? '—';
$hospital = $_GET['hospital'] ?? '—';
$especialidade = $_GET['especialidade'] ?? '—';

$sql = "
    SELECT 
        e.tipo,
        COUNT(*) AS total_instalados,
        MIN(e.custo_aq) AS preco_min,
        MAX(e.custo_aq) AS preco_max
    FROM equipamento e
    JOIN hospitais h ON h.id_hosp = e.id_hosp
    JOIN especialidades esp ON esp.id_esp = e.id_esp
    WHERE h.nome = ?
      AND esp.nome = ?
      AND e.id_hosp IS NOT NULL
    GROUP BY e.tipo
    ORDER BY e.tipo
";

$stmt = db()->prepare($sql);
$stmt->execute([$hospital, $especialidade]);
$equipamentos = $stmt->fetchAll();

function iconEquipamento($tipo) {
    $t = mb_strtolower($tipo);

    if (str_contains($t, 'tac')) return '🖥️';
    if (str_contains($t, 'rm')) return '🧲';
    if (str_contains($t, 'rx')) return '🔭';
    if (str_contains($t, 'angio')) return '🫀';
    if (str_contains($t, 'eco')) return '📡';
    if (str_contains($t, 'holter')) return '📈';
    if (str_contains($t, 'eletro')) return '📈';
    if (str_contains($t, 'monitor')) return '📟';
    if (str_contains($t, 'desfibrilhador')) return '⚡';
    if (str_contains($t, 'ventilador')) return '💨';
    if (str_contains($t, 'bronco')) return '🫁';
    if (str_contains($t, 'cardiotoc')) return '🤰';

    return '🩺';
}

function abbrEquipamento($tipo) {
    $t = mb_strtolower($tipo);

    if (str_contains($t, 'tac')) return 'TAC';
    if (str_contains($t, 'rm')) return 'RM';
    if (str_contains($t, 'rx')) return 'RX';
    if (str_contains($t, 'angio')) return 'ANGIO';
    if (str_contains($t, 'eco')) return 'ECO';
    if (str_contains($t, 'holter')) return 'HOLTER';
    if (str_contains($t, 'eletro')) return 'ECG';
    if (str_contains($t, 'monitor')) return 'MON';
    if (str_contains($t, 'desfibrilhador')) return 'DEF';
    if (str_contains($t, 'ventilador')) return 'VENT';
    if (str_contains($t, 'bronco')) return 'BRONCO';
    if (str_contains($t, 'cardiotoc')) return 'CTG';

    return 'EQ';
}

function descEquipamento($tipo) {
    $t = mb_strtolower($tipo);

    if (str_contains($t, 'tac')) return 'Tomografia computorizada instalada no hospital.';
    if (str_contains($t, 'rm')) return 'Ressonância magnética instalada no hospital.';
    if (str_contains($t, 'rx')) return 'Equipamento de radiologia instalado.';
    if (str_contains($t, 'angio')) return 'Sistema de angiografia instalado.';
    if (str_contains($t, 'eco')) return 'Ecógrafo instalado no serviço.';
    if (str_contains($t, 'holter')) return 'Sistema de monitorização eletrocardiográfica.';
    if (str_contains($t, 'eletro')) return 'Equipamento de ECG instalado.';
    if (str_contains($t, 'monitor')) return 'Monitorização de sinais vitais instalada.';
    if (str_contains($t, 'desfibrilhador')) return 'Equipamento de desfibrilhação instalado.';
    if (str_contains($t, 'ventilador')) return 'Ventilador instalado no serviço.';
    if (str_contains($t, 'bronco')) return 'Equipamento de broncoscopia instalado.';
    if (str_contains($t, 'cardiotoc')) return 'Equipamento de monitorização fetal instalado.';

    return 'Equipamento instalado no hospital.';
}

function euros($valor) {
    if ($valor === null) return '—';
    return number_format((float)$valor, 0, ',', '.') . '€';
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MedDSS — Tipo de Equipamento a Substituir</title>
  <link rel="stylesheet" href="shared.css">
  <style>
    .breadcrumb-bar {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      color: var(--muted);
      margin-bottom: 28px;
      flex-wrap: wrap;
    }

    .breadcrumb-bar a {
      color: var(--teal-dark);
      text-decoration: none;
      font-weight: 500;
    }

    .breadcrumb-bar a:hover {
      text-decoration: underline;
    }

    .breadcrumb-sep {
      color: var(--border);
    }

    .context-strip {
      display: flex;
      align-items: center;
      background: var(--card-bg);
      border: 0.5px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--shadow);
      max-width: 700px;
      margin-bottom: 36px;
    }

    .ctx-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 20px;
      flex: 1;
    }

    .ctx-item+.ctx-item {
      border-left: 0.5px solid var(--border);
    }

    .ctx-emoji {
      font-size: 16px;
      flex-shrink: 0;
    }

    .ctx-label {
      font-size: 10px;
      font-weight: 600;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .ctx-value {
      font-size: 13px;
      font-weight: 600;
      color: var(--text);
      margin-top: 1px;
    }

    .equip-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
      max-width: 980px;
    }

    .equip-card {
      background: var(--card-bg);
      border: 0.5px solid var(--border);
      border-radius: 14px;
      padding: 28px 24px;
      cursor: pointer;
      transition: all 0.18s;
      box-shadow: var(--shadow);
      display: flex;
      flex-direction: column;
      gap: 12px;
      position: relative;
      overflow: hidden;
    }

    .equip-card::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: #2B6CB0;
      transform: scaleX(0);
      transition: transform 0.2s ease;
      transform-origin: left;
    }

    .equip-card:hover {
      border-color: #2B6CB0;
      box-shadow: var(--shadow-lg);
      transform: translateY(-3px);
    }

    .equip-card:hover::after {
      transform: scaleX(1);
    }

    .equip-icon-wrap {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      background: #E6F1FB;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
    }

    .equip-card:hover .equip-icon-wrap {
      background: #D0E6F8;
    }

    .equip-name {
      font-size: 16px;
      font-weight: 700;
      color: var(--text);
      line-height: 1.25;
    }

    .equip-abbr {
      font-size: 11px;
      font-weight: 700;
      color: #2B6CB0;
      background: #E6F1FB;
      padding: 3px 10px;
      border-radius: 99px;
      display: inline-block;
      width: fit-content;
    }

    .equip-desc {
      font-size: 13px;
      color: var(--muted);
      line-height: 1.5;
    }

    .equip-meta {
      display: flex;
      flex-direction: column;
      gap: 4px;
      padding-top: 8px;
      border-top: 0.5px solid var(--sand);
    }

    .equip-meta-row {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      color: var(--muted);
    }

    .equip-meta-dot {
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: var(--border);
      flex-shrink: 0;
    }

    .equip-card-arrow {
      position: absolute;
      right: 18px;
      top: 18px;
      font-size: 16px;
      color: var(--border);
      transition: color 0.15s, right 0.15s;
    }

    .equip-card:hover .equip-card-arrow {
      color: #2B6CB0;
      right: 14px;
    }

    .equip-inv-badge {
      position: absolute;
      top: 16px;
      left: 16px;
      font-size: 10px;
      font-weight: 700;
      background: #E6F1FB;
      color: #2B6CB0;
      padding: 2px 8px;
      border-radius: 99px;
    }

    .equip-search {
      display: flex;
      align-items: center;
      gap: 10px;
      background: var(--card-bg);
      border: 0.5px solid var(--border);
      border-radius: 10px;
      padding: 10px 16px;
      margin-bottom: 24px;
      max-width: 420px;
      box-shadow: var(--shadow);
      transition: border-color 0.15s;
    }

    .equip-search:focus-within {
      border-color: #2B6CB0;
    }

    .equip-search input {
      border: none;
      background: none;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      color: var(--text);
      width: 100%;
      outline: none;
    }

    .equip-search input::placeholder {
      color: var(--muted);
    }

    .info-callout {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      background: #E6F1FB;
      border: 1px solid rgba(43, 108, 176, 0.2);
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 28px;
      max-width: 700px;
      font-size: 13px;
      color: #2B6CB0;
      line-height: 1.5;
    }

    .info-callout-icon {
      font-size: 20px;
      flex-shrink: 0;
      margin-top: 1px;
    }
  </style>
</head>

<body>

<header class="topbar">
  <a class="logo" href="1-regiao.php">Med<span>DSS</span></a>
  <div class="topbar-divider"></div>
  <span class="topbar-sub">Sistema de Apoio à Decisão em Saúde</span>
  <div class="topbar-right">
    <div class="topbar-user">
      <div class="topbar-user">
    <div class="user-avatar">
        <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
    </div>

    <span class="user-name">
        <?= htmlspecialchars($_SESSION['user']['name']) ?>
    </span>

    <a href="logout.php" class="logout-btn">Sair</a>
</div>
  </div>
</header>

<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Progresso</div>
      <div class="sidebar-steps">

        <a href="1-regiao.php" class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Região</div>
            <div class="step-value"><?= htmlspecialchars($regiao) ?></div>
          </div>
        </a>

        <div class="step-connector done"></div>

        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Hospital</div>
            <div class="step-value"><?= htmlspecialchars($hospital) ?></div>
          </div>
        </div>

        <div class="step-connector done"></div>

        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Especialidade</div>
            <div class="step-value"><?= htmlspecialchars($especialidade) ?></div>
          </div>
        </div>

        <div class="step-connector done"></div>

        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Tipo de decisão</div>
            <div class="step-value">Substituir</div>
          </div>
        </div>

        <div class="step-connector done"></div>

        <div class="sidebar-step active">
          <div class="step-num">5</div>
          <div class="step-info">
            <div class="step-label">Tipo de equipamento</div>
            <div class="step-value">Não selecionado</div>
          </div>
        </div>

        <div class="step-connector"></div>

        <div class="sidebar-step locked">
          <div class="step-num">6</div>
          <div class="step-info">
            <div class="step-label">Equip. a substituir</div>
            <div class="step-value">—</div>
          </div>
        </div>

      </div>
    </div>

    <div class="sidebar-info">
      <strong>⇄ Substituição</strong><br>
      Selecione o tipo de equipamento instalado que pretende substituir em
      <span><?= htmlspecialchars($especialidade) ?></span>.
      <br><br>
      Só serão mostrados os equipamentos desse tipo presentes no inventário do hospital.
    </div>
  </aside>

  <main class="main">
    <div class="breadcrumb-bar">
      <a href="1-regiao.php">Início</a>
      <span class="breadcrumb-sep">/</span>

      <a href="2-hospital.php?regiao=<?= urlencode($regiao) ?>">
        <?= htmlspecialchars($regiao) ?>
      </a>
      <span class="breadcrumb-sep">/</span>

      <a href="3-especialidade.php?regiao=<?= urlencode($regiao) ?>&hospital=<?= urlencode($hospital) ?>">
        <?= htmlspecialchars($hospital) ?>
      </a>
      <span class="breadcrumb-sep">/</span>

      <a href="4-decisao.php?regiao=<?= urlencode($regiao) ?>&hospital=<?= urlencode($hospital) ?>&especialidade=<?= urlencode($especialidade) ?>">
        <?= htmlspecialchars($especialidade) ?>
      </a>
      <span class="breadcrumb-sep">/</span>

      <a href="4-decisao.php?regiao=<?= urlencode($regiao) ?>&hospital=<?= urlencode($hospital) ?>&especialidade=<?= urlencode($especialidade) ?>">
        Substituir
      </a>
      <span class="breadcrumb-sep">/</span>

      <span>Tipo de equipamento</span>
    </div>

    <div class="context-strip">
      <div class="ctx-item">
        <div class="ctx-emoji">🏥</div>
        <div>
          <div class="ctx-label">Hospital</div>
          <div class="ctx-value"><?= htmlspecialchars($hospital) ?></div>
        </div>
      </div>

      <div class="ctx-item">
        <div class="ctx-emoji">🩺</div>
        <div>
          <div class="ctx-label">Especialidade</div>
          <div class="ctx-value"><?= htmlspecialchars($especialidade) ?></div>
        </div>
      </div>

      <div class="ctx-item">
        <div class="ctx-emoji">⇄</div>
        <div>
          <div class="ctx-label">Decisão</div>
          <div class="ctx-value">Substituição</div>
        </div>
      </div>
    </div>

    <div class="info-callout">
      <div class="info-callout-icon">ℹ️</div>
      <div>
        Selecione o <strong>tipo de equipamento</strong> instalado que pretende analisar para substituição.
        O passo seguinte mostrará os equipamentos desse tipo presentes no inventário do hospital,
        com o respetivo estado de urgência.
      </div>
    </div>

    <div class="page-header">
      <div class="page-title">Tipo de equipamento</div>
      <div class="page-sub">
        Selecione o tipo instalado que pretende substituir em <?= htmlspecialchars($especialidade) ?>
      </div>
    </div>

    <div class="equip-search">
      <span style="font-size:15px;color:var(--muted)">🔍</span>
      <input placeholder="Pesquisar tipo de equipamento..." oninput="filterEquip(this.value)">
    </div>

    <div class="equip-grid" id="equip-grid">

      <?php if (count($equipamentos) === 0): ?>

        <p>Não existem equipamentos instalados para esta especialidade neste hospital.</p>

      <?php else: ?>

        <?php foreach ($equipamentos as $eq): ?>
          <?php
            $tipo = $eq['tipo'];
            $abbr = abbrEquipamento($tipo);

            $url = '4b-equipamento-atual.php?'
              . 'regiao=' . urlencode($regiao)
              . '&hospital=' . urlencode($hospital)
              . '&especialidade=' . urlencode($especialidade)
              . '&filtroTipo=' . urlencode($tipo)
              . '&filtroAbbr=' . urlencode($abbr);
          ?>

          <div class="equip-card"
               data-search="<?= htmlspecialchars(mb_strtolower($tipo . ' ' . $abbr . ' ' . descEquipamento($tipo))) ?>"
               onclick="window.location.href='<?= $url ?>'">

            <div class="equip-card-arrow">›</div>

            <div class="equip-inv-badge">
              <?= (int)$eq['total_instalados'] ?> no inventário
            </div>

            <div class="equip-icon-wrap" style="margin-top:20px">
              <?= iconEquipamento($tipo) ?>
            </div>

            <div>
              <div class="equip-name">
                <?= htmlspecialchars($tipo) ?>
              </div>
              <div class="equip-abbr">
                <?= htmlspecialchars($abbr) ?>
              </div>
            </div>

            <div class="equip-desc">
              <?= htmlspecialchars(descEquipamento($tipo)) ?>
            </div>

            <div class="equip-meta">
              <div class="equip-meta-row">
                <div class="equip-meta-dot"></div>
                <?= (int)$eq['total_instalados'] ?>
                equipamento<?= (int)$eq['total_instalados'] !== 1 ? 's' : '' ?>
                instalado<?= (int)$eq['total_instalados'] !== 1 ? 's' : '' ?>
              </div>

              <div class="equip-meta-row">
                <div class="equip-meta-dot"></div>
                Gama de substituição:
                <?= euros($eq['preco_min']) ?> – <?= euros($eq['preco_max']) ?>
              </div>
            </div>

          </div>

        <?php endforeach; ?>

      <?php endif; ?>

    </div>
  </main>
</div>

<script>
function filterEquip(q) {
  const query = q.toLowerCase();
  const cards = document.querySelectorAll('.equip-card');

  cards.forEach(card => {
    const text = card.dataset.search || '';
    card.style.display = text.includes(query) ? 'flex' : 'none';
  });
}
</script>

</body>
</html>
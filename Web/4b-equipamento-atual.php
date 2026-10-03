<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/api/config.php';

$regiao        = $_GET['regiao'] ?? '—';
$hospital      = $_GET['hospital'] ?? '—';
$especialidade = $_GET['especialidade'] ?? '—';

$filtroTipo = $_GET['filtroTipo'] ?? null;
$filtroAbbr = $_GET['filtroAbbr'] ?? null;

$sql = "
    SELECT
        e.id_equip,
        e.tipo,
        e.fabricante,
        e.modelo,
        e.data_install,
        e.t_vida_util,
        e.custo_man
    FROM equipamento e
    JOIN hospitais h ON h.id_hosp = e.id_hosp
    JOIN especialidades esp ON esp.id_esp = e.id_esp
    WHERE h.nome = ?
      AND esp.nome = ?
      AND e.id_hosp IS NOT NULL
";

$params = [$hospital, $especialidade];

if (!empty($filtroTipo)) {
    $sql .= " AND e.tipo = ?";
    $params[] = $filtroTipo;
}

$sql .= " ORDER BY e.data_install ASC";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$equipamentos = $stmt->fetchAll();

function calcularUrgencia($dataInstall, $vidaUtil) {
    if (!$dataInstall || !$vidaUtil) {
        return [
            'classe' => 'medium',
            'label' => 'Atenção',
            'restante' => '—'
        ];
    }

    $anoAtual = (int) date('Y');
    $anoInstalacao = (int) date('Y', strtotime($dataInstall));
    $idade = $anoAtual - $anoInstalacao;
    $restante = (int)$vidaUtil - $idade;

    if ($restante <= 2) {
        return [
            'classe' => 'high',
            'label' => 'Urgente',
            'restante' => $restante . ' ano(s)'
        ];
    }

    if ($restante <= 5) {
        return [
            'classe' => 'medium',
            'label' => 'Atenção',
            'restante' => $restante . ' ano(s)'
        ];
    }

    return [
        'classe' => 'low',
        'label' => 'Aceitável',
        'restante' => $restante . ' ano(s)'
    ];
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedDSS — Equipamento a Substituir</title>
<link rel="stylesheet" href="shared.css">
<style>
  .breadcrumb-bar {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--muted);
    margin-bottom: 28px; flex-wrap: wrap;
  }
  .breadcrumb-bar a { color: var(--teal-dark); text-decoration: none; font-weight: 500; }
  .breadcrumb-bar a:hover { text-decoration: underline; }
  .breadcrumb-sep { color: var(--border); }

  .context-strip {
    display: flex; align-items: center;
    background: var(--card-bg); border: 0.5px solid var(--border);
    border-radius: 12px; overflow: hidden;
    box-shadow: var(--shadow); max-width: 700px; margin-bottom: 36px;
  }
  .ctx-item { display: flex; align-items: center; gap: 10px; padding: 14px 20px; flex: 1; }
  .ctx-item + .ctx-item { border-left: 0.5px solid var(--border); }
  .ctx-emoji { font-size: 16px; flex-shrink: 0; }
  .ctx-label { font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
  .ctx-value { font-size: 13px; font-weight: 600; color: var(--text); margin-top: 1px; }

  /* Inventory list */
  .inventory-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    max-width: 900px;
  }
  .inventory-count {
    font-size: 13px; color: var(--muted);
    background: var(--sand); padding: 5px 14px;
    border-radius: 99px; font-weight: 500;
  }

  .inv-search {
    display: flex; align-items: center; gap: 10px;
    background: var(--card-bg); border: 0.5px solid var(--border);
    border-radius: 10px; padding: 10px 16px;
    max-width: 360px; box-shadow: var(--shadow);
    transition: border-color 0.15s;
  }
  .inv-search:focus-within { border-color: var(--teal); }
  .inv-search input {
    border: none; background: none;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px; color: var(--text); width: 100%; outline: none;
  }
  .inv-search input::placeholder { color: var(--muted); }

  /* Inventory table */
  .inv-table {
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    max-width: 960px;
  }

  .inv-table-header {
    display: grid;
    grid-template-columns: 2fr 1.2fr 1fr 1fr 1.2fr 80px;
    gap: 0;
    padding: 13px 22px;
    background: var(--sand);
    border-bottom: 0.5px solid var(--border);
    font-size: 11px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
  }

  .inv-row {
    display: grid;
    grid-template-columns: 2fr 1.2fr 1fr 1fr 1.2fr 80px;
    gap: 0;
    padding: 16px 22px;
    border-bottom: 0.5px solid var(--sand);
    align-items: center;
    cursor: pointer;
    transition: background 0.15s;
  }
  .inv-row:last-child { border-bottom: none; }
  .inv-row:hover { background: #FDFCFA; }

  .inv-equip-name { font-size: 14px; font-weight: 600; color: var(--text); }
  .inv-equip-type { font-size: 12px; color: var(--muted); margin-top: 2px; }

  .inv-cell { font-size: 13px; color: var(--text); font-weight: 400; }

  /* Urgency badge */
  .urgency {
    font-size: 11px; font-weight: 700;
    padding: 4px 10px; border-radius: 99px;
    display: inline-block; width: fit-content;
  }
  .urgency.high { background: #FDE8E2; color: #993C1D; }
  .urgency.medium { background: #FEF3CD; color: #856404; }
  .urgency.low { background: var(--teal-light); color: var(--teal-dark); }

  .select-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--teal); color: #fff;
    border: none; border-radius: 8px;
    padding: 8px 14px;
    font-family: 'DM Sans', sans-serif;
    font-size: 12px; font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    white-space: nowrap;
  }
  .select-btn:hover { background: var(--teal-dark); }

  /* Legend */
  .legend {
    display: flex; align-items: center; gap: 20px;
    margin-bottom: 16px;
    font-size: 12px; color: var(--muted);
  }
  .legend-item { display: flex; align-items: center; gap: 6px; }
  .legend-dot { width: 10px; height: 10px; border-radius: 50%; }
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

  <div class="sidebar-step done">
    <div class="step-num">✓</div>
    <div class="step-info">
      <div class="step-label">Tipo de equipamento</div>
      <div class="step-value"><?= htmlspecialchars($filtroTipo ?? 'Todos') ?></div>
    </div>
  </div>

  <div class="step-connector done"></div>

  <div class="sidebar-step active">
    <div class="step-num">6</div>
    <div class="step-info">
      <div class="step-label">Equipamento atual</div>
      <div class="step-value">Não selecionado</div>
    </div>
  </div>

</div>
    <div class="sidebar-info">
      <strong>⇄ Substituição</strong>
      Selecione o equipamento instalado no hospital que pretende substituir. A urgência é calculada com base na vida útil e estado de manutenção.
    </div>
  </aside>

  <main class="main">
    <div class="breadcrumb-bar">
      <a href="1-regiao.php">Início</a>
      <span class="breadcrumb-sep">/</span>
      <a href="2-hospital.php?regiao=<?= urlencode($regiao) ?>"><?= htmlspecialchars($regiao) ?></a>
      <span class="breadcrumb-sep">/</span>
      <a href="3-especialidade.php?regiao=<?= urlencode($regiao) ?>&hospital=<?= urlencode($hospital) ?>"><?= htmlspecialchars($hospital) ?></a>
      <span class="breadcrumb-sep">/</span>
      <a href="4-decisao.php?regiao=<?= urlencode($regiao) ?>&hospital=<?= urlencode($hospital) ?>&especialidade=<?= urlencode($especialidade) ?>"><?= htmlspecialchars($especialidade) ?></a>
      <span class="breadcrumb-sep">/</span>
      <a id="bc-decisao" href="#">Substituir</a>
      <span class="breadcrumb-sep">/</span>
      <span>Equipamento atual</span>
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

    <div class="page-header">
      <div class="page-title">Equipamento a substituir</div>
      <div class="page-sub" id="page-sub">Inventário de equipamentos instalados no hospital</div>
    </div>

    <div class="inventory-header">
      <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:#993C1D"></div> Substituição urgente</div>
        <div class="legend-item"><div class="legend-dot" style="background:#C9960C"></div> Atenção recomendada</div>
        <div class="legend-item"><div class="legend-dot" style="background:var(--teal)"></div> Estado aceitável</div>
      </div>
      <div class="inventory-count">
  <?= count($equipamentos) ?> equipamento(s)
</div>
      <div class="inv-search">
        <span style="font-size:15px;color:var(--muted)">🔍</span>
        <input placeholder="Pesquisar equipamento..." oninput="filterInv(this.value)" id="inv-search-input">
      </div>
    </div>

    <div class="inv-table">
      <div class="inv-table-header">
        <div>Equipamento</div>
        <div>Tipo</div>
        <div>Ano instal.</div>
        <div>Vida útil rest.</div>
        <div>Urgência</div>
        <div></div>
      </div>
      <div id="inv-body">

  <?php if (count($equipamentos) === 0): ?>

    <div style="padding:32px;text-align:center;color:var(--muted);font-size:14px;">
      Nenhum equipamento encontrado para este hospital e especialidade.
    </div>

  <?php else: ?>

    <?php foreach ($equipamentos as $eq): ?>

      <?php
        $urg = calcularUrgencia($eq['data_install'], $eq['t_vida_util']);

        $ano = $eq['data_install']
          ? date('Y', strtotime($eq['data_install']))
          : '—';

        $nomeEquipamento = trim($eq['fabricante'] . ' ' . $eq['modelo']);

        $url = '5b-substituir.php?'
          . 'regiao=' . urlencode($regiao)
          . '&hospital=' . urlencode($hospital)
          . '&especialidade=' . urlencode($especialidade)
          . '&equipAtualId=' . urlencode($eq['id_equip'])
          . '&equipAtualNome=' . urlencode($nomeEquipamento)
          . '&equipAtualTipo=' . urlencode($eq['tipo'])
          . '&equipAtualAno=' . urlencode($ano);
      ?>

      <div class="inv-row"
           data-search="<?= htmlspecialchars(strtolower($nomeEquipamento . ' ' . $eq['tipo'])) ?>"
           onclick="window.location.href='<?= $url ?>'">

        <div>
          <div class="inv-equip-name">
            <?= htmlspecialchars($nomeEquipamento) ?>
          </div>
          <div class="inv-equip-type">
            <?= htmlspecialchars($eq['tipo']) ?>
          </div>
        </div>

        <div class="inv-cell">
          <?= htmlspecialchars($eq['tipo']) ?>
        </div>

        <div class="inv-cell">
          <?= htmlspecialchars($ano) ?>
        </div>

        <div class="inv-cell">
          <?= htmlspecialchars($urg['restante']) ?>
        </div>

        <div>
          <span class="urgency <?= htmlspecialchars($urg['classe']) ?>">
            <?= htmlspecialchars($urg['label']) ?>
          </span>
        </div>

        <div>
          <button class="select-btn"
                  onclick="event.stopPropagation(); window.location.href='<?= $url ?>'">
            Selecionar →
          </button>
        </div>

      </div>

    <?php endforeach; ?>

  <?php endif; ?>

</div>
    </div>
  </main>
</div>

<script>
function filterInv(q) {
  const query = q.toLowerCase();
  const rows = document.querySelectorAll('.inv-row');

  rows.forEach(row => {
    const text = row.dataset.search || '';
    row.style.display = text.includes(query) ? 'grid' : 'none';
  });
}
</script>
</body>
</html>

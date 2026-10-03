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

$stmt = db()->prepare("
    SELECT 
        e.tipo,
        COUNT(*) AS total_modelos,
        MIN(e.custo_aq) AS preco_min,
        MAX(e.custo_aq) AS preco_max,
        MIN(e.prazo_ent) AS prazo_min,
        MAX(e.prazo_ent) AS prazo_max
    FROM equipamento e
    JOIN especialidades esp ON esp.id_esp = e.id_esp
    WHERE esp.nome = ?
      AND e.id_hosp IS NULL
    GROUP BY e.tipo
    ORDER BY e.tipo
");

$stmt->execute([$especialidade]);
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

function abreviaturaEquipamento($tipo) {
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

function descricaoEquipamento($tipo) {
    $t = mb_strtolower($tipo);

    if (str_contains($t, 'tac')) return 'Tomografia computorizada para diagnóstico por imagem.';
    if (str_contains($t, 'rm')) return 'Ressonância magnética para imagem de tecidos moles.';
    if (str_contains($t, 'rx')) return 'Equipamento de radiologia digital.';
    if (str_contains($t, 'angio')) return 'Sistema de angiografia para diagnóstico e intervenção vascular.';
    if (str_contains($t, 'eco')) return 'Ecógrafo para diagnóstico por ultrassons.';
    if (str_contains($t, 'holter')) return 'Monitorização eletrocardiográfica ambulatória.';
    if (str_contains($t, 'eletro')) return 'Equipamento para realização de ECG.';
    if (str_contains($t, 'monitor')) return 'Monitorização de sinais vitais.';
    if (str_contains($t, 'desfibrilhador')) return 'Equipamento de desfibrilhação e emergência cardíaca.';
    if (str_contains($t, 'ventilador')) return 'Suporte ventilatório invasivo ou não invasivo.';
    if (str_contains($t, 'bronco')) return 'Equipamento para broncoscopia.';
    if (str_contains($t, 'cardiotoc')) return 'Monitorização fetal e uterina.';

    return 'Equipamento hospitalar associado à especialidade selecionada.';
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
<title>MedDSS — Tipo de Equipamento</title>
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

  /* Equipment type grid */
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
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: var(--teal);
    transform: scaleX(0);
    transition: transform 0.2s ease;
    transform-origin: left;
  }
  .equip-card:hover {
    border-color: var(--teal);
    box-shadow: var(--shadow-lg);
    transform: translateY(-3px);
  }
  .equip-card:hover::after { transform: scaleX(1); }

  .equip-icon-wrap {
    width: 56px; height: 56px;
    border-radius: 14px;
    background: var(--teal-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
  }
  .equip-name { font-size: 16px; font-weight: 700; color: var(--text); line-height: 1.25; }
  .equip-abbr {
    font-size: 11px; font-weight: 700;
    color: var(--teal-dark);
    background: var(--teal-light);
    padding: 3px 10px; border-radius: 99px;
    display: inline-block; width: fit-content;
  }
  .equip-desc { font-size: 13px; color: var(--muted); line-height: 1.5; }

  .equip-meta {
    display: flex; flex-direction: column; gap: 4px;
    padding-top: 8px;
    border-top: 0.5px solid var(--sand);
  }
  .equip-meta-row { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--muted); }
  .equip-meta-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--border); flex-shrink: 0; }

  .equip-card-arrow {
    position: absolute;
    right: 18px; top: 18px;
    font-size: 16px; color: var(--border);
    transition: color 0.15s, right 0.15s;
  }
  .equip-card:hover .equip-card-arrow { color: var(--teal); right: 14px; }

  /* Search bar */
  .equip-search {
    display: flex; align-items: center; gap: 10px;
    background: var(--card-bg); border: 0.5px solid var(--border);
    border-radius: 10px; padding: 10px 16px;
    margin-bottom: 24px; max-width: 420px;
    box-shadow: var(--shadow); transition: border-color 0.15s;
  }
  .equip-search:focus-within { border-color: var(--teal); }
  .equip-search input {
    border: none; background: none;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px; color: var(--text); width: 100%; outline: none;
  }
  .equip-search input::placeholder { color: var(--muted); }
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
            <div class="step-value">Adquirir</div>
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
      </div>
    </div>
    <div class="sidebar-info">
      <strong>🛒 Aquisição nova</strong>
      Selecione o tipo de equipamento que pretende adquirir para a especialidade de <span><?= htmlspecialchars($especialidade) ?></span>.
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
      <a id="bc-decisao" href="#">Adquirir</a>
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
        <div class="ctx-emoji">🛒</div>
        <div>
          <div class="ctx-label">Decisão</div>
          <div class="ctx-value">Aquisição nova</div>
        </div>
      </div>
    </div>

    <div class="page-header">
      <div class="page-title">Tipo de equipamento</div>
      <div class="page-sub">
  Selecione o tipo de equipamento a adquirir em <?= htmlspecialchars($especialidade) ?>
</div>
    </div>

    <div class="equip-search">
      <span style="font-size:15px;color:var(--muted)">🔍</span>
      <input placeholder="Pesquisar tipo de equipamento..." oninput="filterEquip(this.value)">
    </div>

    <div class="equip-grid" id="equip-grid">

  <?php if (count($equipamentos) === 0): ?>

    <p>Não existem equipamentos registados para esta especialidade.</p>

  <?php else: ?>

    <?php foreach ($equipamentos as $eq): ?>
      <?php
        $tipo = $eq['tipo'];
        $url = '5a-adquirir.php?regiao=' . urlencode($regiao)
             . '&hospital=' . urlencode($hospital)
             . '&especialidade=' . urlencode($especialidade)
             . '&tipo=' . urlencode($tipo);
      ?>

      <div class="equip-card" data-name="<?= htmlspecialchars($tipo) ?>" onclick="window.location.href='<?= $url ?>'">
        <div class="equip-card-arrow">›</div>

        <div class="equip-icon-wrap">
          <?= iconEquipamento($tipo) ?>
        </div>

        <div>
          <div class="equip-name"><?= htmlspecialchars($tipo) ?></div>
          <div class="equip-abbr"><?= abreviaturaEquipamento($tipo) ?></div>
        </div>

        <div class="equip-desc">
          <?= descricaoEquipamento($tipo) ?>
        </div>

        <div class="equip-meta">
          <div class="equip-meta-row">
            <div class="equip-meta-dot"></div>
            <?= (int)$eq['total_modelos'] ?> modelo(s) disponíveis
          </div>

          <div class="equip-meta-row">
            <div class="equip-meta-dot"></div>
            Gama de preços:
            <?= euros($eq['preco_min']) ?> – <?= euros($eq['preco_max']) ?>
          </div>

          <div class="equip-meta-row">
            <div class="equip-meta-dot"></div>
            Prazo: <?= (int)$eq['prazo_min'] ?> – <?= (int)$eq['prazo_max'] ?> dias
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
  const cards = document.querySelectorAll('.equip-card');
  const query = q.toLowerCase();

  cards.forEach(card => {
    const name = card.dataset.name.toLowerCase();
    card.style.display = name.includes(query) ? 'flex' : 'none';
  });
}
</script>
</body>
</html>

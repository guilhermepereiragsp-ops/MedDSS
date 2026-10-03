<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/api/config.php';
$regioes = db()->query("
    SELECT id_regiao, nome_regiao
    FROM regioes
    ORDER BY id_regiao
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedDSS — Selecionar Região</title>
<link rel="stylesheet" href="shared.css">
<style>
  /* Map page specifics */
  .map-container {
    display: grid;
    grid-template-columns: 480px 1fr;
    gap: 48px;
    align-items: start;
  }

  .map-card {
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: 16px;
    padding: 32px;
    box-shadow: var(--shadow-md);
    position: sticky;
    top: calc(var(--topbar-h) + 48px);
  }

  .map-card svg { width: 100%; height: auto; display: block; }

  .region-path {
    fill: #E1F5EE;
    stroke: var(--card-bg);
    stroke-width: 2.5;
    cursor: pointer;
    transition: fill 0.15s ease;
  }
  .region-path:hover { fill: #5DCAA5; }
  .region-path.selected { fill: #1D9E75; }
  .region-label {
    font-family: 'DM Sans', sans-serif;
    font-size: 10px;
    font-weight: 700;
    fill: #0F6E56;
    text-anchor: middle;
    pointer-events: none;
    dominant-baseline: central;
  }

  /* Region list panel */
  .region-panel { display: flex; flex-direction: column; gap: 0; }
  .region-panel-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 12px;
  }

  .region-list-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.15s;
    border: 0.5px solid transparent;
    margin-bottom: 6px;
    background: var(--card-bg);
    border-color: var(--border);
    box-shadow: var(--shadow);
  }
  .region-list-item:hover {
    background: #F5F4F0;
    border-color: #B5B3AB;
    transform: translateX(3px);
  }
  .region-list-item.selected {
    background: var(--teal-light);
    border-color: var(--teal);
    box-shadow: 0 0 0 1px var(--teal);
  }

  .region-dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    background: var(--border);
    flex-shrink: 0;
    transition: background 0.15s;
  }
  .region-list-item.selected .region-dot { background: var(--teal); }
  .region-list-item:hover .region-dot { background: var(--teal-mid); }

  .region-list-name { font-size: 15px; font-weight: 500; color: var(--text); flex: 1; }
  .region-list-item.selected .region-list-name { font-weight: 600; color: var(--teal-dark); }

  .region-arrow { font-size: 14px; color: var(--muted); transition: transform 0.15s; }
  .region-list-item:hover .region-arrow { transform: translateX(3px); }

  .proceed-wrap {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 0.5px solid var(--border);
  }
  .proceed-hint {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 14px;
  }
  .proceed-hint span {
    color: var(--teal-dark);
    font-weight: 600;
  }
</style>
</head>
<body>

<!-- TOP BAR -->
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
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Progresso</div>
      <div class="sidebar-steps">
        <a href="1-regiao.php" class="sidebar-step active">
          <div class="step-num">1</div>
          <div class="step-info">
            <div class="step-label">Região</div>
            <div class="step-value" id="sb-region">Não selecionado</div>
          </div>
        </a>
        <div class="step-connector"></div>
        <div class="sidebar-step locked">
          <div class="step-num">2</div>
          <div class="step-info">
            <div class="step-label">Hospital</div>
            <div class="step-value">—</div>
          </div>
        </div>
        <div class="step-connector"></div>
        <div class="sidebar-step locked">
          <div class="step-num">3</div>
          <div class="step-info">
            <div class="step-label">Especialidade</div>
            <div class="step-value">—</div>
          </div>
        </div>
        <div class="step-connector"></div>
        <div class="sidebar-step locked">
          <div class="step-num">4</div>
          <div class="step-info">
            <div class="step-label">Tipo de decisão</div>
            <div class="step-value">—</div>
          </div>
        </div>
      </div>
    </div>

    <div class="sidebar-info">
      <strong>💡 Como funciona</strong>
      Selecione a região, hospital e especialidade para obter recomendações de equipamento baseadas em dados clínicos reais.
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">
    <div class="page-header">
      <div class="page-title">Selecionar região</div>
      <div class="page-sub">Escolha a região de saúde para filtrar os hospitais disponíveis</div>
    </div>

    <div class="map-container">
      <!-- MAP -->
      <div class="map-card">
        <svg viewBox="0 0 320 480" xmlns="http://www.w3.org/2000/svg">
          <!-- Norte -->
          <path id="map-norte" class="region-path"
            d="M50,24 L200,24 L205,44 L192,64 L198,90 L178,108 L155,102 L132,114 L108,108 L82,114 L62,104 L48,108 L36,90 L42,64 L36,44 Z"
            onclick="selectRegion('Norte')"/>
          <text class="region-label" x="120" y="68">Norte</text>

          <!-- Centro -->
          <path id="map-centro" class="region-path"
            d="M48,108 L62,104 L82,114 L108,108 L132,114 L155,102 L178,108 L198,90 L205,130 L188,154 L192,185 L172,202 L148,196 L124,202 L100,192 L74,196 L55,180 L42,156 L48,130 Z"
            onclick="selectRegion('Centro')"/>
          <text class="region-label" x="123" y="150">Centro</text>

          <!-- Lisboa e VT -->
          <path id="map-lvt" class="region-path"
            d="M55,180 L74,196 L100,192 L124,202 L148,196 L172,202 L178,228 L160,248 L136,242 L112,252 L88,244 L64,252 L48,234 L44,208 Z"
            onclick="selectRegion('Lisboa e Vale do Tejo')"/>
          <text class="region-label" x="112" y="216">Lisboa e VT</text>

          <!-- Alentejo -->
          <path id="map-alentejo" class="region-path"
            d="M64,252 L88,244 L112,252 L136,242 L160,248 L178,228 L192,260 L184,296 L168,320 L144,330 L120,322 L96,330 L72,318 L56,292 L50,264 Z"
            onclick="selectRegion('Alentejo')"/>
          <text class="region-label" x="120" y="284">Alentejo</text>

          <!-- Algarve -->
          <path id="map-algarve" class="region-path"
            d="M72,318 L96,330 L120,322 L144,330 L168,320 L180,342 L170,360 L144,366 L120,362 L96,366 L74,358 L62,338 Z"
            onclick="selectRegion('Algarve')"/>
          <text class="region-label" x="121" y="342">Algarve</text>

          <!-- Açores -->
          <path id="map-acores" class="region-path"
            d="M24,400 L80,400 L86,414 L80,428 L66,434 L36,434 L24,426 L20,412 Z"
            onclick="selectRegion('Açores')"/>
          <text class="region-label" x="53" y="416">Açores</text>

          <!-- Madeira -->
          <path id="map-madeira" class="region-path"
            d="M110,400 L166,400 L172,414 L166,428 L152,434 L122,434 L110,426 L106,412 Z"
            onclick="selectRegion('Madeira')"/>
          <text class="region-label" x="139" y="416">Madeira</text>

          <!-- Island subtexts -->
          <text x="53" y="446" font-family="DM Sans,sans-serif" font-size="9" fill="#888780" text-anchor="middle">(Arquipélago)</text>
          <text x="139" y="446" font-family="DM Sans,sans-serif" font-size="9" fill="#888780" text-anchor="middle">(Arquipélago)</text>
        </svg>
      </div>

      <!-- REGION LIST -->
<div class="region-panel">
  <div class="region-panel-title"><?= count($regioes) ?> Regiões disponíveis</div>

  <?php foreach ($regioes as $regiao): ?>
    <div class="region-list-item"
         id="ri-<?= htmlspecialchars($regiao['nome_regiao']) ?>"
         onclick="selectRegion('<?= htmlspecialchars($regiao['nome_regiao']) ?>')">
      <div class="region-dot"></div>
      <span class="region-list-name">
        <?= htmlspecialchars($regiao['nome_regiao']) ?>
      </span>
      <span class="region-arrow">›</span>
    </div>
  <?php endforeach; ?>

  <div class="proceed-wrap" id="proceed-wrap" style="display:none;">
    <div class="proceed-hint">Região selecionada: <span id="selected-name"></span></div>
    <a id="proceed-link" href="#" class="cta-btn">Continuar para hospitais →</a>
  </div>
</div>

<script>
  const MAP_IDS = {
    'Norte': 'map-norte',
    'Centro': 'map-centro',
    'Lisboa e Vale do Tejo': 'map-lvt',
    'Alentejo': 'map-alentejo',
    'Algarve': 'map-algarve',
    'Açores': 'map-acores',
    'Madeira': 'map-madeira'
  };

  function selectRegion(name) {
    document.querySelectorAll('.region-path').forEach(p => p.classList.remove('selected'));
    document.querySelectorAll('.region-list-item').forEach(i => i.classList.remove('selected'));

    const mapEl = document.getElementById(MAP_IDS[name]);
    if (mapEl) mapEl.classList.add('selected');

    const listEl = document.getElementById('ri-' + name);
    if (listEl) listEl.classList.add('selected');

    document.getElementById('sb-region').textContent = name;
    document.getElementById('proceed-wrap').style.display = 'block';
    document.getElementById('selected-name').textContent = name;

    const encoded = encodeURIComponent(name);
    document.getElementById('proceed-link').href = `2-hospital.php?regiao=${encoded}`;
  }
</script>
</body>
</html>

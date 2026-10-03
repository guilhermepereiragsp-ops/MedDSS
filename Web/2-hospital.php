<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/api/config.php';

$nome_regiao = $_GET['regiao'] ?? 'Norte';

$stmt = db()->prepare("
    SELECT 
        h.id_hosp,
        h.nome
    FROM hospitais h
    JOIN regioes r ON r.id_regiao = h.id_regiao
    WHERE r.nome_regiao = ?
    ORDER BY h.nome
");

$stmt->execute([$nome_regiao]);
$hospitais = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedDSS — Selecionar Hospital</title>
<link rel="stylesheet" href="shared.css">

<style>
  .hospital-card {
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: var(--radius-modal);
    padding: 24px;
    cursor: pointer;
    transition: all 0.15s;
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: 8px;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
  }

  .hospital-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 3px; height: 100%;
    background: var(--teal);
    transform: scaleY(0);
    transform-origin: bottom;
    transition: transform 0.2s ease;
    border-radius: 0 3px 3px 0;
  }

  .hospital-card:hover {
    border-color: #B5B3AB;
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .hospital-card:hover::before { transform: scaleY(1); }

  .hospital-card .card-icon {
    font-size: 32px;
    margin-bottom: 4px;
    display: block;
  }

  .hospital-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    line-height: 1.3;
  }

  .hospital-type {
    font-size: 12px;
    font-weight: 600;
    color: var(--teal-dark);
    background: var(--teal-light);
    padding: 3px 10px;
    border-radius: 99px;
    display: inline-block;
    width: fit-content;
  }

  .hospital-meta {
    display: flex;
    flex-direction: column;
    gap: 3px;
    margin-top: 6px;
  }

  .hospital-meta-item {
    font-size: 12px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .hospital-card-arrow {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: var(--border);
    transition: color 0.15s, right 0.15s;
  }

  .hospital-card:hover .hospital-card-arrow {
    color: var(--teal);
    right: 16px;
  }

  .search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: 10px;
    padding: 10px 16px;
    margin-bottom: 24px;
    max-width: 480px;
    box-shadow: var(--shadow);
    transition: border-color 0.15s;
  }

  .search-bar:focus-within { border-color: var(--teal); }

  .search-icon {
    font-size: 16px;
    color: var(--muted);
    flex-shrink: 0;
  }

  .search-input {
    border: none;
    background: none;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--text);
    width: 100%;
    outline: none;
  }

  .search-input::placeholder { color: var(--muted); }

  .stats-row {
    display: flex;
    gap: 24px;
    margin-bottom: 32px;
    padding: 20px 24px;
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: 12px;
    box-shadow: var(--shadow);
  }

  .stat-item {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .stat-num {
    font-size: 22px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -0.5px;
  }

  .stat-label {
    font-size: 12px;
    color: var(--muted);
    font-weight: 400;
  }

  .stat-divider {
    width: 0.5px;
    background: var(--border);
    align-self: stretch;
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
            <div class="step-value"><?= htmlspecialchars($nome_regiao) ?></div>
          </div>
        </a>

        <div class="step-connector done"></div>

        <div class="sidebar-step active">
          <div class="step-num">2</div>
          <div class="step-info">
            <div class="step-label">Hospital</div>
            <div class="step-value">Não selecionado</div>
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
      <strong>🏥 Hospitais públicos</strong>
      Listagem de hospitais e centros hospitalares na região selecionada do SNS.
    </div>
  </aside>

  <main class="main">
    <a href="1-regiao.php" class="back-btn">← Voltar</a>

    <div class="page-header">
      <div class="page-title">Selecionar hospital</div>
      <div class="page-sub">
        Hospitais na região <?= htmlspecialchars($nome_regiao) ?>
      </div>
    </div>

    <div class="stats-row">
      <div class="stat-item">
        <div class="stat-num"><?= count($hospitais) ?></div>
        <div class="stat-label">Hospitais disponíveis</div>
      </div>

      <div class="stat-divider"></div>

      <div class="stat-item">
        <div class="stat-num">SNS</div>
        <div class="stat-label">Rede nacional</div>
      </div>

      <div class="stat-divider"></div>

      <div class="stat-item">
        <div class="stat-num"><?= htmlspecialchars($nome_regiao) ?></div>
        <div class="stat-label">Região selecionada</div>
      </div>
    </div>

    <div class="search-bar">
      <span class="search-icon">🔍</span>
      <input class="search-input" placeholder="Pesquisar hospital..." oninput="filterHospitals(this.value)">
    </div>

    <div class="card-grid card-grid-2" id="hospital-grid">

      <?php if (count($hospitais) === 0): ?>

        <p>Não existem hospitais registados para esta região.</p>

      <?php else: ?>

        <?php foreach ($hospitais as $hospital): ?>
          <div class="hospital-card"
               data-hospital="<?= htmlspecialchars($hospital['nome']) ?>">
            <span class="card-icon">🏥</span>

            <div class="hospital-name">
              <?= htmlspecialchars($hospital['nome']) ?>
            </div>

            <div class="hospital-type">Hospital público</div>

            <div class="hospital-meta">
              <div class="hospital-meta-item">
                📍 <?= htmlspecialchars($nome_regiao) ?>
              </div>
            </div>

            <div class="hospital-card-arrow">›</div>
          </div>
        <?php endforeach; ?>

      <?php endif; ?>

    </div>
  </main>
</div>

<script>
  const regiao = <?= json_encode($nome_regiao, JSON_UNESCAPED_UNICODE) ?>;

  function goHospital(name) {
    const r = encodeURIComponent(regiao);
    const h = encodeURIComponent(name);
    window.location.href = `3-especialidade.php?regiao=${r}&hospital=${h}`;
  }

  document.querySelectorAll('.hospital-card').forEach(card => {
    card.addEventListener('click', function () {
      goHospital(this.dataset.hospital);
    });
  });

  function filterHospitals(query) {
    const cards = document.querySelectorAll('.hospital-card');

    cards.forEach(card => {
      const name = card.querySelector('.hospital-name').textContent.toLowerCase();
      card.style.display = name.includes(query.toLowerCase()) ? 'flex' : 'none';
    });
  }
</script>

</body>
</html>
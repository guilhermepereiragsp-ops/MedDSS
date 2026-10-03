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
?>

<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MedDSS — Tipo de Decisão</title>
<link rel="stylesheet" href="shared.css">
<style>
  .decision-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    max-width: 900px;
    margin-top: 36px;
  }

  .decision-card {
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: 16px;
    padding: 36px 32px;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: var(--shadow-md);
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
  }
  .decision-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
  }
  .decision-card.teal-card:hover { border-color: var(--teal); }
  .decision-card.blue-card:hover { border-color: var(--blue); }

  .decision-icon {
    width: 64px; height: 64px;
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 30px;
    font-weight: 700;
  }
  .decision-icon.teal { background: var(--teal-light); color: var(--teal); }
  .decision-icon.blue { background: var(--blue-light); color: var(--blue); }

  .decision-title { font-size: 22px; font-weight: 700; color: var(--text); letter-spacing: -0.4px; }
  .decision-sub { font-size: 14px; color: var(--muted); line-height: 1.55; }

  .decision-features { display: flex; flex-direction: column; gap: 8px; margin-top: 4px; }
  .decision-feature {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--text);
  }
  .feature-check {
    width: 20px; height: 20px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
    font-weight: 700;
  }
  .teal-card .feature-check { background: var(--teal-light); color: var(--teal-dark); }
  .blue-card .feature-check { background: var(--blue-light); color: var(--blue); }

  .decision-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: all 0.15s;
    width: fit-content;
  }
  .decision-cta.teal { background: var(--teal); color: #fff; }
  .decision-cta.blue { background: var(--blue); color: #fff; }
  .decision-cta:hover { opacity: 0.9; transform: translateX(2px); }

  /* Context strip */
  .context-strip {
    display: flex;
    align-items: center;
    gap: 0;
    background: var(--card-bg);
    border: 0.5px solid var(--border);
    border-radius: 12px;
    padding: 0;
    margin-bottom: 36px;
    overflow: hidden;
    box-shadow: var(--shadow);
    max-width: 700px;
  }
  .ctx-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 22px;
    flex: 1;
  }
  .ctx-item + .ctx-item { border-left: 0.5px solid var(--border); }
  .ctx-emoji { font-size: 18px; flex-shrink: 0; }
  .ctx-label { font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
  .ctx-value { font-size: 13px; font-weight: 600; color: var(--text); margin-top: 1px; white-space: nowrap; }

  .breadcrumb-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 28px;
    flex-wrap: wrap;
  }
  .breadcrumb-bar a { color: var(--teal-dark); text-decoration: none; font-weight: 500; }
  .breadcrumb-bar a:hover { text-decoration: underline; }
  .breadcrumb-sep { color: var(--border); }
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
            <div class="step-value" id="sb-region"><?= htmlspecialchars($regiao) ?></div>
          </div>
        </a>
        <div class="step-connector done"></div>
        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Hospital</div>
            <div class="step-value" id="sb-hospital"><?= htmlspecialchars($hospital) ?></div>
          </div>
        </div>
        <div class="step-connector done"></div>
        <div class="sidebar-step done">
          <div class="step-num">✓</div>
          <div class="step-info">
            <div class="step-label">Especialidade</div>
            <div class="step-value" id="sb-spec"><?= htmlspecialchars($especialidade) ?></div>
          </div>
        </div>
        <div class="step-connector done"></div>
        <div class="sidebar-step active">
          <div class="step-num">4</div>
          <div class="step-info">
            <div class="step-label">Tipo de decisão</div>
            <div class="step-value">Não selecionado</div>
          </div>
        </div>
      </div>
    </div>
    <div class="sidebar-info">
      <strong>⚖️ Que tipo de pedido?</strong>
      "Adquirir" para novos equipamentos. "Substituir" para comparar com o equipamento existente.
    </div>
  </aside>

  <main class="main">
    <div class="breadcrumb-bar">
      <a href="1-regiao.php">Início</a>
      <span class="breadcrumb-sep">/</span>
      <a id="bc-regiao" href="2-hospital.php?regiao=<?= urlencode($regiao) ?>">
  <?= htmlspecialchars($regiao) ?>
</a>
      <span class="breadcrumb-sep">/</span>
      <a id="bc-hospital" href="3-especialidade.php?regiao=<?= urlencode($regiao) ?>&hospital=<?= urlencode($hospital) ?>">
  <?= htmlspecialchars($hospital) ?>
</a>
      <span class="breadcrumb-sep">/</span>
      <span id="bc-spec"><?= htmlspecialchars($especialidade) ?></span>
    </div>

    <div class="context-strip">
      <div class="ctx-item">
        <div class="ctx-emoji">📍</div>
        <div>
          <div class="ctx-label">Região</div>
          <div class="ctx-value" id="ctx-region"><?= htmlspecialchars($regiao) ?></div>
        </div>
      </div>
      <div class="ctx-item">
        <div class="ctx-emoji">🏥</div>
        <div>
          <div class="ctx-label">Hospital</div>
          <div class="ctx-value" id="ctx-hospital"><?= htmlspecialchars($hospital) ?></div>
        </div>
      </div>
      <div class="ctx-item">
        <div class="ctx-emoji">🩺</div>
        <div>
          <div class="ctx-label">Especialidade</div>
          <div class="ctx-value" id="ctx-spec"><?= htmlspecialchars($especialidade) ?></div>
        </div>
      </div>
    </div>

    <div class="page-header">
      <div class="page-title">Tipo de decisão</div>
      <div class="page-sub">O que pretende fazer? Selecione uma opção para continuar</div>
    </div>

    <div class="decision-grid">
      <div class="decision-card teal-card" id="card-adquirir">
        <div class="decision-icon teal">＋</div>
        <div>
          <div class="decision-title">Adquirir</div>
          <div class="decision-sub">Novo equipamento para o serviço. O sistema recomenda a melhor opção baseada no perfil clínico e orçamental.</div>
        </div>
        <div class="decision-features">
          <div class="decision-feature"><div class="feature-check">✓</div> Recomendação IA baseada em dados</div>
          <div class="decision-feature"><div class="feature-check">✓</div> Score de adequação clínica</div>
          <div class="decision-feature"><div class="feature-check">✓</div> Análise custo-benefício</div>
        </div>
        <button class="decision-cta teal">Ver recomendação →</button>
      </div>

      <div class="decision-card blue-card" id="card-substituir">
        <div class="decision-icon blue">⇄</div>
        <div>
          <div class="decision-title">Substituir</div>
          <div class="decision-sub">Comparar com o equipamento existente. Análise detalhada dos ganhos técnicos e financeiros da substituição.</div>
        </div>
        <div class="decision-features">
          <div class="decision-feature"><div class="feature-check">✓</div> Comparativo lado a lado</div>
          <div class="decision-feature"><div class="feature-check">✓</div> ROI e custo total de posse</div>
          <div class="decision-feature"><div class="feature-check">✓</div> Análise de vida útil residual</div>
        </div>
        <button class="decision-cta blue">Comparar equipamentos →</button>
      </div>
    </div>
  </main>
</div>

<script>
  const base = "regiao=<?= urlencode($regiao) ?>&hospital=<?= urlencode($hospital) ?>&especialidade=<?= urlencode($especialidade) ?>";

  document.getElementById('card-adquirir').onclick = () => {
    window.location.href = `4a-tipo-equipamento.php?${base}`;
  };

  document.getElementById('card-adquirir').querySelector('.decision-cta').onclick = (e) => {
    e.stopPropagation();
    window.location.href = `4a-tipo-equipamento.php?${base}`;
  };

  document.getElementById('card-substituir').onclick = () => {
    window.location.href = `4b1-tipo-substituicao.php?${base}`;
  };

  document.getElementById('card-substituir').querySelector('.decision-cta').onclick = (e) => {
    e.stopPropagation();
    window.location.href = `4b1-tipo-substituicao.php?${base}`;
  };
</script>
</body>
</html>
